<?php

namespace App\Entity;

use App\Model\UserCreated;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    public function __construct(
        #[ORM\Column(length: 180)]
        private string $email,

        #[ORM\Column(length: 255)]
        private string $firstname,

        #[ORM\Column(length: 255)]
        private string $lastname,

        #[ORM\Column(length: 15, nullable: true)]
        private ?string $phone = null,

        /**
         * @var list<string> The user roles
         */
        #[ORM\Column]
        private array $roles = ['ROLE_USER'],
    ) {
    }

    public static function create(UserCreated $userCreatedAdmin): self
    {
        return new self(
            $userCreatedAdmin->email,
            $userCreatedAdmin->firstname,
            $userCreatedAdmin->lastname,
            $userCreatedAdmin->phone,
            $userCreatedAdmin->roles,
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
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
}
