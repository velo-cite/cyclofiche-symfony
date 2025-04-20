<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Entity\User;
use App\Form\IssueType;
use App\Model\Issue\IssueCreated;
use App\Repository\IssueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/issue')]
final class IssueController extends AbstractController
{
    #[Route(name: 'app_issue_index', methods: ['GET'])]
    public function index(IssueRepository $issueRepository): Response
    {
        return $this->render('issue/index.html.twig', [
            'issues' => $issueRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_report_an_issue', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IssueType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var IssueCreated $issueCreated */
            $issueCreated = $form->getData();

            if ($user = $this->getUser()) {
                if ($user instanceof User) {
                    $issueCreated->setCreatedBy($user);
                }
            }

            $entityManager->persist(Issue::createFromIssueCreated($issueCreated));
            $entityManager->flush();

            // todo launch event for IssueCreated

            return $this->redirectToRoute('app_issue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('issue/new.html.twig', [
            'form' => $form,
        ]);
    }
}
