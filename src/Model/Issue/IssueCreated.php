<?php

namespace App\Model\Issue;

use App\Entity\IssueCategory;
use App\Entity\User;

class IssueCreated
{
    public function __construct(
        public ?IssueCategory $category = null,
        public ?string  $location = null,
        public ?string  $city = null,
        public ?string  $address = null,
        public ?string  $description = null,
        public ?User   $creator = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $email = null,
        public ?string $phone = null,
    )
    {
    }

    public function setCreatedBy(User $creator): void
    {
        $this->firstname = $creator->getFirstname();
        $this->lastname = $creator->getLastname();
        $this->email = $creator->getEmail();
        $this->phone = $creator->getPhone();
    }
}