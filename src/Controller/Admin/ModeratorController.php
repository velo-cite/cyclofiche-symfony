<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Moderator;
use App\Event\Admin\ModeratorCreatedEvent;
use App\Form\Admin\ModeratorType;
use App\Repository\Admin\ModeratorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/admin/moderator')]
final class ModeratorController extends AbstractController
{
    #[Route(name: 'app_admin_moderator_index', methods: ['GET'])]
    public function index(ModeratorRepository $moderatorRepository): Response
    {
        return $this->render('admin/moderator/index.html.twig', [
            'moderators' => $moderatorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_moderator_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher): Response
    {
        $form = $this->createForm(ModeratorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moderatorCreated = $form->getData();
            $moderator = Moderator::create($moderatorCreated);
            $entityManager->persist($moderator);
            $entityManager->flush();

            $event = new ModeratorCreatedEvent($moderator);
            $eventDispatcher->dispatch($event);

            return $this->redirectToRoute('app_admin_moderator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/moderator/new.html.twig', [
            'form' => $form,
        ]);
    }
}
