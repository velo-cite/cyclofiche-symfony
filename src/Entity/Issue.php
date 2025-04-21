<?php

namespace App\Entity;

use App\Entity\Admin\Moderator;
use App\Model\Admin\IssueAccepted;
use App\Model\Issue\IssueCreated;
use App\Model\Issue\IssueStatut;
use App\Repository\IssueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IssueRepository::class)]
class Issue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Photo>
     */
    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'issue', orphanRemoval: true)]
    private Collection $photos;

    #[ORM\Column(length: 255)]
    private ?string $state = IssueStatut::SUBMITTED->value;

    #[ORM\ManyToOne(targetEntity: Moderator::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Moderator $moderator = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentModerator = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'issues')]
        #[ORM\JoinColumn(nullable: false)]
        private IssueCategory $category,

        #[ORM\Column(type: Types::TEXT)]
        private string $location,

        #[ORM\Column(length: 255)]
        private string $city,

        #[ORM\Column(length: 255)]
        private string $address,

        #[ORM\Column(type: Types::TEXT)]
        private string $description,

        #[ORM\Column(length: 255)]
        private string $firstname,

        #[ORM\Column(length: 255)]
        private string $lastname,

        #[ORM\Column(length: 255)]
        private string $email,

        #[ORM\ManyToOne(inversedBy: 'issues')]
        #[ORM\JoinColumn(nullable: true)]
        private ?User $creator = null,

        #[ORM\Column(length: 255)]
        private ?string $phone = null,
    ) {
        $this->photos = new ArrayCollection();
    }

    public static function createFromIssueCreated(IssueCreated $created): self
    {
        if (!$created->creator && (!$created->firstname || !$created->lastname || !$created->email)) {
            throw new \LogicException('The user or the (email and firstname and lastname) must be set');
        }

        return new self(
            $created->category,
            $created->location,
            $created->city,
            $created->address,
            $created->description,
            $created->firstname,
            $created->lastname,
            $created->email,
            $created->creator,
            $created->phone,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setIssue($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getIssue() === $this) {
                $photo->setIssue(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCategory(): ?IssueCategory
    {
        return $this->category;
    }

    public function setCategory(?IssueCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function acceptFromIssueAccepted(IssueAccepted $issueAccepted, Moderator $moderator): void
    {
        $this->moderator = $moderator;
        $this->commentModerator = $issueAccepted->comment;
    }
}
