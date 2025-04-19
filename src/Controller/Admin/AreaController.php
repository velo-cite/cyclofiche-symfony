<?php

namespace App\Controller\Admin;

use App\Entity\Area;
use App\Form\AreaType;
use App\Repository\AreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/area')]
final class AreaController extends AbstractController
{
    #[Route(name: 'app_admin_area_index', methods: ['GET'])]
    public function index(AreaRepository $areaRepository): Response
    {
        return $this->render('admin/area/index.html.twig', [
            'areas' => $areaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_area_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $area = new Area();
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($area);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/area/new.html.twig', [
            'area' => $area,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_area_show', methods: ['GET'])]
    public function show(Area $area): Response
    {
        return $this->render('admin/area/show.html.twig', [
            'area' => $area,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_area_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Area $area, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/area/edit.html.twig', [
            'area' => $area,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_area_delete', methods: ['POST'])]
    public function delete(Request $request, Area $area, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$area->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($area);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_area_index', [], Response::HTTP_SEE_OTHER);
    }
}
