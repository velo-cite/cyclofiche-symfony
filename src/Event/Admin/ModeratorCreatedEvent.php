<?php

namespace App\Event\Admin;

use App\Entity\Admin\Moderator;
use Symfony\Contracts\EventDispatcher\Event;

class ModeratorCreatedEvent extends Event
{
    public function __construct(
        private readonly Moderator $moderator
    ) {
    }

    public function getModerator(): Moderator
    {
        return $this->moderator;
    }
}