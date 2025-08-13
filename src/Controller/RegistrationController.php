<?php

namespace App\Controller;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Model\User\UserRegistered;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] UserRegistered $dto,
        ValidatorInterface $validator,
    ): JsonResponse
    {
        try {
            $errors = $validator->validate($dto);
        } catch (\Exception $e) {
            return $this->json(['errors' => (string) $e->getMessage()], 400);
        }
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $user = User::register($dto);
        $user->definePassword($userPasswordHasher->hashPassword($user, $dto->password));

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            return $this->json(['error' => 'Cet email est déjà utilisé'], 409);
        }

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app-verify-email', $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@velo-cite.org', 'No Reply Velo-Cité'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->json(['message' => $this->translator->trans('front.user.confirmationEmailSend')], 201);
    }

    #[Route('/api/verify/email', name: 'api_verify_email')]
    public function verifyUserEmail(
        Request $request,
        UserRepository $userRepository,
        JWTTokenManagerInterface $JWTManager,
        RefreshTokenManagerInterface $refreshTokenManager
    ): JsonResponse
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->json(['error' => 'ID manquant'], 400);
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->json([
                'error' => $this->translator->trans($exception->getReason(), [], 'VerifyEmailBundle'),
            ], 400);
        }

        $this->addFlash('success', $this->translator->trans('front.user.confirmationEmailDone'));
        $token = $JWTManager->create($user);

        // Génération du RefreshToken (valide par ex. 1 mois)
        $refreshToken = new RefreshToken();
        $refreshToken->setUsername($user->getUserIdentifier());
        $refreshToken->setRefreshToken(bin2hex(random_bytes(64)));
        $refreshToken->setValid((new \DateTime())->modify('+1 month'));
        $refreshTokenManager->save($refreshToken);

        return $this->json([
            'message' => $this->translator->trans('front.user.confirmationEmailDone'),
            'token' => $token,
            'refresh_token' => $refreshToken->getRefreshToken(),
        ]);
    }
}
