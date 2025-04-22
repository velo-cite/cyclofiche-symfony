<?php

namespace App\Model\Admin;

use App\Entity\Issue;

class IssueRejected
{
    public function __construct(
        public Issue $issue,
        public ?string $comment = null,
    )
    {
    }
}