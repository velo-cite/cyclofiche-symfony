<?php

namespace App\Controller\Admin;

use App\Repository\IssueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/issue')]
final class IssueController extends AbstractController
{
    #[Route(name: 'app_admin_issue_index', methods: ['GET'])]
    public function index(IssueRepository $issueRepository): Response
    {
        return $this->render('admin/issue/index.html.twig', [
            'issues' => $issueRepository->findAll(),
        ]);
    }
}
