<?php

namespace App\Model\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegistered
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public string $email,
        #[Assert\NotBlank]
        public string $firstname,

        #[Assert\NotBlank]
        public string $lastname,
        #[Assert\NotBlank]
        public string $phone,
        #[Assert\PasswordStrength]
        #[Assert\NotCompromisedPassword]
        #[Assert\NotBlank]
        public string $password,
    ) {
    }
}
