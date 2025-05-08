<?php

namespace App\Entity\Admin;

use App\Entity\Organisation;
use App\Model\Admin\OrganisationUserAdded;
use App\Repository\OrganisationUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: OrganisationUserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL_ORGA_USER', fields: ['email'])]
class OrganisationUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = ['ROLE_METROPOLE'];

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Organisation::class)]
        private Organisation $organisation,

        #[ORM\Column(length: 180)]
        private string $email,

        #[ORM\Column]
        private string $firstname,

        #[ORM\Column]
        private string $lastname,
    )
    {
    }

    public static function create(OrganisationUserAdded $organisationUserAdded): self
    {
        return new self(
            $organisationUserAdded->organisation,
            $organisationUserAdded->email,
            $organisationUserAdded->firstname,
            $organisationUserAdded->lastname,
        );
    }

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
