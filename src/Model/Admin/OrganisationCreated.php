<?php

namespace App\Model\Admin;


class OrganisationCreated
{
    public function __construct(
        public string $libelle,
    ) {
    }
}