<?php

namespace App\Entity;

use App\Model\User\UserRegistered;
use App\Model\UserCreated;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'issue:read'])]
    private ?int $id = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    /**
     * @var Collection<int, Issue>
     */
    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'creator')]
    private Collection $issues;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private bool $isVerified = false;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?string $emailCrypted = null;

    public function __construct(
        #[ORM\Column(length: 180)]
        #[Groups(['user:read'])]
        private string $email,
        #[ORM\Column(length: 255)]
        #[Groups(['user:read', 'issue:read'])]
        private string $firstname,
        #[ORM\Column(length: 255)]
        #[Groups(['user:read', 'issue:read'])]
        private string $lastname,
        #[ORM\Column(length: 15, nullable: true)]
        #[Groups(['user:read'])]
        private ?string $phone = null,
    ) {
        $this->issues = new ArrayCollection();
        $this->emailCrypted = md5($this->email);
    }

    public static function create(UserCreated $userCreatedAdmin): self
    {
        return new self(
            $userCreatedAdmin->email,
            $userCreatedAdmin->firstname,
            $userCreatedAdmin->lastname,
            $userCreatedAdmin->phone,
        );
    }

    public static function register(UserRegistered $userRegistered): self
    {
        return new self(
            $userRegistered->email,
            $userRegistered->firstname,
            $userRegistered->lastname,
            $userRegistered->phone,
        );
    }

    public function updatePassword(string $password): void
    {
        $this->password = $password;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getEmailCrypted(): ?string
    {
        return $this->emailCrypted;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }

    public function definePassword(string $hashPassword): void
    {
        $this->password = $hashPassword;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return Collection<int, Issue>
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue): static
    {
        if (!$this->issues->contains($issue)) {
            $this->issues->add($issue);
            $issue->setCreator($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): static
    {
        if ($this->issues->removeElement($issue)) {
            // set the owning side to null (unless already changed)
            if ($issue->getCreator() === $this) {
                $issue->setCreator(null);
            }
        }

        return $this;
    }

    public function confirmeAccount(): void
    {
        $this->isVerified = true;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }
}
