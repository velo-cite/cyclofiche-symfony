<?php

namespace App\Event\Admin;

use App\Entity\Admin\OrganisationUser;
use Symfony\Contracts\EventDispatcher\Event;

class OrganisationUserAddedEvent extends Event
{
    public function __construct(
        private readonly OrganisationUser $organisationUser
    ) {
    }

    public function getOrganisationUser(): OrganisationUser
    {
        return $this->organisationUser;
    }
}