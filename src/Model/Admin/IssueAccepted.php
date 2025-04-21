<?php

namespace App\Model\Admin;

use App\Entity\Issue;

class IssueAccepted
{
    public function __construct(
        public Issue $issue,
        public ?string $comment = null,
    )
    {
    }
}