<?php

namespace App\Model\Admin;

class ModeratorCreated
{
    public function __construct(
        public ?string $email = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $phone = null,
    ) {
    }
}
