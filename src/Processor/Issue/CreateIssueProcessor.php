<?php

namespace App\Processor\Issue;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Issue;
use App\Entity\User;
use App\Model\Issue\IssueCreated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @implements ProcessorInterface<IssueCreated, Issue>
 */
final class CreateIssueProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private WorkflowInterface $cycloficheStateMachine,
    ) {}

    /**
     * @param IssueCreated $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Issue
    {
        $user = $this->security->getUser();

        if ($user instanceof User && $data instanceof IssueCreated) {
            $data->setCreatedBy($user);
        }
        $issue = Issue::createFromIssueCreated($data);

        try {
            $this->cycloficheStateMachine->apply($issue, 'to_check');
        } catch (\LogicException) {
            throw new AccessDeniedHttpException('Forbidden to report an issue');
        }

        $this->entityManager->persist($issue);
        $this->entityManager->flush();

        $issue->setCreator(null);

        return $issue;
    }
}
