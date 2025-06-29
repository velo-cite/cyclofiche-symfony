<?php

namespace App\Model\Issue;

use App\Entity\IssueCategory;
use App\Entity\User;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

class IssueCreated
{
    #[NotBlank]
    #[Groups(['issue:create'])]
    public IssueCategory $category;
    #[NotBlank]
    #[Groups(['issue:create'])]
    public string $description;
    #[Groups(['issue:create'])]
    public ?string $location = null;
    #[Groups(['issue:create'])]
    public ?string $city = null;
    #[Groups(['issue:create'])]
    public ?string $address = null;
    #[Groups(['issue:create'])]
    public ?User $creator = null;
    #[Groups(['issue:create'])]
    public ?string $firstname = null;
    #[Groups(['issue:create'])]
    public ?string $lastname = null;
    #[Groups(['issue:create'])]
    public ?string $email = null;
    #[Groups(['issue:create'])]
    public ?string $phone = null;

    public function setCreatedBy(User $creator): void
    {
        $this->creator = $creator;
        $this->firstname = $creator->getFirstname();
        $this->lastname = $creator->getLastname();
        $this->email = $creator->getEmail();
        $this->phone = $creator->getPhone();
    }
}
