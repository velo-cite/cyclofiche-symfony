<?php

namespace App\Controller;

use App\Entity\Admin\Moderator;
use App\Entity\Admin\OrganisationUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/api/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/request', name: 'api_reset_password_request', methods: ['POST'])]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return $this->json(['error' => 'Email is required'], 400);
        }

        return $this->processSendingPasswordResetEmail($email, $mailer, $translator);
    }

    #[Route('/confirm', name: 'api_reset_password_confirm', methods: ['POST'])]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;
        $plainPassword = $data['password'] ?? null;

        if (!$token || !$plainPassword) {
            return $this->json(['error' => 'Token and password are required'], 400);
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json([
                'error' => sprintf(
                    '%s - %s',
                    $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                    $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
                )
            ], 400);
        }

        // Supprimer le token après usage
        $this->resetPasswordHelper->removeResetRequest($token);

        // Mettre à jour le mot de passe
        $user->definePassword($passwordHasher->hashPassword($user, $plainPassword));
        $this->entityManager->flush();

        $this->cleanSessionAfterReset();

        return $this->json(['message' => 'Password updated successfully']);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, TranslatorInterface $translator): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $emailFormData])
            ?? $this->entityManager->getRepository(Moderator::class)->findOneBy(['email' => $emailFormData])
            ?? $this->entityManager->getRepository(OrganisationUser::class)->findOneBy(['email' => $emailFormData]);

        // Ne pas révéler si l'email existe
        if (!$user) {
            return $this->json(['message' => 'If the email exists, a reset link has been sent']);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json(['message' => 'If the email exists, a reset link has been sent']);
        }

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@velo-cite.org', 'Velo-Cité'))
            ->to((string) $user->getEmail())
            ->subject($translator->trans('front.forgotpassword.email.yourPasswordResetRequest'))
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $mailer->send($email);

        return $this->json(['message' => 'If the email exists, a reset link has been sent']);
    }
}
