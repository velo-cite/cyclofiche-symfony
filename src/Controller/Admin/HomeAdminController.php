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

#[Route('/admin/')]
final class HomeAdminController extends AbstractController
{
    #[Route(name: 'app_admin_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }
}
