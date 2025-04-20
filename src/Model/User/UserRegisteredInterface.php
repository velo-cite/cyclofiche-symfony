<?php

namespace App\Model\User;

interface UserRegisteredInterface
{
    public function isActivated(): bool;
}
