<?php

namespace App\Model\Admin;

use App\Entity\Organisation;

class OrganisationUserAdded
{
    public function __construct(
        public Organisation $organisation,
        public ?string $email = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
    )
    {
    }
}
