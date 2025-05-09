<?php

namespace App\Entity;

use App\Entity\Admin\Moderator;
use App\Entity\Admin\OrganisationUser;
use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Moderator $moderator = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?OrganisationUser $organisationUser = null;

    public function __construct(User|Moderator|OrganisationUser $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        if ($user instanceof User) {
            $this->user = $user;
        } elseif ($user instanceof Moderator) {
            $this->moderator = $user;
        } elseif ($user instanceof OrganisationUser) {
            $this->organisationUser = $user;
        }

        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User|Moderator|OrganisationUser
    {
        return $this->user ?? $this->moderator ?? $this->organisationUser;
    }
}
