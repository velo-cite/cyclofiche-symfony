<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Model\Admin\IssueCategoryCreated;
use App\Repository\IssueCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: IssueCategoryRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(normalizationContext: ['groups' => ['issueCategory:read']]),
    ]
)]
class IssueCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['issue:read', 'issueCategory:read'])]
    private ?int $id = null;

    /**
     * @var Collection<int, Issue>
     */
    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'category')]
    private Collection $issues;

    public function __construct(
        #[ORM\Column(length: 255)]
        #[Groups(['issue:read', 'issueCategory:read'])]
        private string $libelle,

        #[ORM\Column(length: 255)]
        #[Groups(['issue:read', 'issueCategory:read'])]
        private ?string $image = 'image.png',
    ) {
        $this->issues = new ArrayCollection();
    }

    public static function createFromIssueCategoryCreated(IssueCategoryCreated $issueCategoryCreated): self
    {
        return new self(
            $issueCategoryCreated->libelle,
            $issueCategoryCreated->image,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
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
            $issue->setCategory($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): static
    {
        if ($this->issues->removeElement($issue)) {
            // set the owning side to null (unless already changed)
            if ($issue->getCategory() === $this) {
                $issue->setCategory(null);
            }
        }

        return $this;
    }
}
