<?php

namespace App\Entity;

use App\Repository\OrganisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
class Organisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $libelle,

        /**
         * @var Collection<int, Area>
         */
        #[ORM\ManyToMany(targetEntity: Area::class, inversedBy: 'organisations', cascade: ['persist'])]
        private Collection $areas,
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * @return Collection<int, Area>
     */
    public function getAreas(): Collection
    {
        return $this->areas;
    }
}
