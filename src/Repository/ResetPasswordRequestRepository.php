<?php

namespace App\Repository;

use App\Entity\Admin\Moderator;
use App\Entity\Admin\OrganisationUser;
use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Proxy\DefaultProxyClassNameResolver;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use function PHPUnit\Framework\matches;

/**
 * @extends ServiceEntityRepository<ResetPasswordRequest>
 */
class ResetPasswordRequestRepository extends ServiceEntityRepository implements ResetPasswordRequestRepositoryInterface
{
    use ResetPasswordRequestRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordRequest::class);
    }

    /**
     * @param User $user
     */
    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface
    {
        return new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);
    }

    /**
     * Remove a users ResetPasswordRequest objects from persistence.
     *
     * Warning - This is a destructive operation. Calling this method
     * may have undesired consequences for users who have valid
     * ResetPasswordRequests but have not "checked their email" yet.
     *
     * @see https://github.com/SymfonyCasts/reset-password-bundle?tab=readme-ov-file#advanced-usage
     */
    public function removeRequests(object $user): void
    {
        $field = $this->guestUserObject($user);

        $query = $this->createQueryBuilder('t')
            ->delete()
            ->where("$field = :user")
            ->setParameter('user', $user)
        ;

        $query->getQuery()->execute();
    }

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
        $field = $this->guestUserObject($user);

        // Normally there is only 1 max request per use, but written to be flexible
        /** @var ResetPasswordRequestInterface $resetPasswordRequest */
        $resetPasswordRequest = $this->createQueryBuilder('t')
            ->where("$field = :user")
            ->setParameter('user', $user)
            ->orderBy('t.requestedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null !== $resetPasswordRequest && !$resetPasswordRequest->isExpired()) {
            return $resetPasswordRequest->getRequestedAt();
        }

        return null;
    }

    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $object = $resetPasswordRequest->getUser();
        $field = $this->guestUserObject($object);
        $this->createQueryBuilder('t')
            ->delete()
            ->where("$field = :user")
            ->setParameter('user', $object)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param object $object
     * @return string
     */
    public function guestUserObject(object $object): string
    {
        return match (DefaultProxyClassNameResolver::getClass($object)) {
            User::class => 't.user',
            Moderator::class => 't.moderator',
            OrganisationUser::class => 't.organisationUser',
        };
    }
}
