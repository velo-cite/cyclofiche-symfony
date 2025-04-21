<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Moderator;
use App\Entity\Issue;
use App\Form\Admin\IssueModerationType;
use App\Model\Admin\IssueAccepted;
use App\Model\Issue\IssueStatut;
use App\Repository\IssueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/issue')]
final class IssueController extends AbstractController
{
    public function __construct(
        private WorkflowInterface $cycloficheStateMachine,
        private TranslatorInterface $translator,
    )
    {
    }

    #[Route(name: 'app_admin_issue_index', methods: ['GET'])]
    public function index(IssueRepository $issueRepository): Response
    {
        return $this->render('admin/issue/index.html.twig', [
            'issues' => $issueRepository->findAll(),
        ]);
    }

    #[Route(path: '/{id}', name: 'app_admin_issue_show', methods: ['GET'])]
    public function show(Issue $issue): Response
    {
        return $this->render('admin/issue/show.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route(path: '/accept/{id}', name: 'app_admin_issue_accept', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function accept(Request $request, Issue $issue, EntityManagerInterface $entityManager): Response
    {
        if (!$this->cycloficheStateMachine->can($issue, 'review_metropole')) {
            $this->redirectToRoute('app_admin_issue_show', ['id' => $issue->getId()]);
        }
        $issueAccepted = new IssueAccepted($issue);
        $form = $this->createForm(IssueModerationType::class, $issueAccepted);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Moderator $user */
            $user = $this->getUser();
            $user->acceptIssue($issueAccepted);

            $this->cycloficheStateMachine->apply($issue, 'to_check_metropole');
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('admin.moderator.issue_accepted'));
            return $this->redirectToRoute('app_admin_issue_index');
        }

        return $this->render('admin/issue/accept.html.twig', [
            'issue' => $issue,
            'form' => $form->createView(),
        ]);
    }
}
