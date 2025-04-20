<?php

namespace App\Model\User;

class UserRegistered
{
    public function __construct(
        public ?string $email = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $phone = null,
    ) {
    }
}
