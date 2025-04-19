<?php

namespace App\Model;

class UserCreated
{
    public function __construct(
        public string $email,
        public string $firstname,
        public string $lastname,
        public array $roles = [],
        public ?string $phone = null,
    )
    {
    }
}