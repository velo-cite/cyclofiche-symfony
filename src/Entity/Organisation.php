<?php

namespace App\Entity;

use App\Model\Admin\OrganisationCreated;
use App\Repository\OrganisationRepository;
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
    ) {
    }

    public static function create(OrganisationCreated $organisationCreated): self
    {
        return new self(
            $organisationCreated->libelle,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }
}
