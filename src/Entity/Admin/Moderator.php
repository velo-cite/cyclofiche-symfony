<?php

namespace App\Entity\Admin;

use App\Entity\Issue;
use App\Model\Admin\IssueAccepted;
use App\Model\Admin\ModeratorCreated;
use App\Model\User\UserRegistered;
use App\Model\UserCreated;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: '`Moderator`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL_MODERATOR', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Moderator implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = ['ROLE_MODERATOR'];

    public function __construct(
        #[ORM\Column(length: 180)]
        private string $email,
        #[ORM\Column(length: 255)]
        private string $firstname,
        #[ORM\Column(length: 255)]
        private string $lastname,
        #[ORM\Column(length: 15, nullable: true)]
        private ?string $phone = null,
    ) {
    }

    public static function create(ModeratorCreated $moderatorCreated): self
    {
        return new self(
            $moderatorCreated->email,
            $moderatorCreated->firstname,
            $moderatorCreated->lastname,
            $moderatorCreated->phone,
        );
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function updatePassword(string $pass): void
    {
        $this->password = $pass;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function acceptIssue(IssueAccepted $issueAccepted): void
    {
        $issueAccepted->issue->acceptFromIssueAccepted($issueAccepted, $this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
}
