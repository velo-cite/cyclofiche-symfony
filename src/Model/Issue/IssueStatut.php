<?php

namespace App\Model\Issue;

enum IssueStatut: string
{

    case SUBMITTED = 'submitted';
    case REVIEW_MODERATOR = 'review_moderator';
    case REVIEW_METROPOLE = 'review_metropole';
    case DONE = 'done';
    case IGNORED = 'ignored';
}
