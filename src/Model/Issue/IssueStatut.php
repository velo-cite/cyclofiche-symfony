<?php

namespace App\Model\Issue;

enum IssueStatut: string
{
    case WAITING = 'waiting';
    case IN_PROGRESS = 'in_progress';
    case ANSWERED = 'answered';
    case PROCESSED = 'processed';
}
