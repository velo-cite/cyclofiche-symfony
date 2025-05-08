<?php

namespace App\Controller\Admin;

use App\Entity\Admin\OrganisationUser;
use App\Entity\Organisation;
use App\Event\OrganisationUserAddedEvent;
use App\Form\Admin\OrganisationUserAddedType;
use App\Form\Admin\OrganisationUserType;
use App\Model\Admin\OrganisationUserAdded;
use App\Repository\OrganisationUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/admin/organisation/user')]
final class OrganisationUserController extends AbstractController
{
    #[Route('/{organisation}/new', name: 'app_admin_organisation_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        Organisation $organisation,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
    ): Response
    {
        $organisationUserAdded = new OrganisationUserAdded($organisation);
        $form = $this->createForm(OrganisationUserAddedType::class, $organisationUserAdded);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organisationUser = OrganisationUser::create($organisationUserAdded);
            $entityManager->persist($organisationUser);
            $entityManager->flush();

            $event = new OrganisationUserAddedEvent($organisationUser);
            $eventDispatcher->dispatch($event);

            return $this->redirectToRoute('app_admin_organisation_show', ['id' => $organisation->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/organisation_user/new.html.twig', [
            'organisation_user' => $organisationUserAdded,
            'organisation' => $organisation,
            'form' => $form,
        ]);
    }
}
