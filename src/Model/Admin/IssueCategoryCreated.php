<?php

namespace App\Model\Admin;

class IssueCategoryCreated
{
    public function __construct(
        public ?string $libelle = null,
        public ?string $image = 'image.png'
    )
    {
    }
}