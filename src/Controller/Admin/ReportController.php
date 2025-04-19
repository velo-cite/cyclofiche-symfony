<?php

namespace App\Controller\Admin;

use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/report')]
final class ReportController extends AbstractController
{
    #[Route(name: 'app_admin_report_index', methods: ['GET'])]
    public function index(ReportRepository $reportRepository): Response
    {
        return $this->render('admin/report/index.html.twig', [
            'reports' => $reportRepository->findAll(),
        ]);
    }
}
