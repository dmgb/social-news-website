<?php declare(strict_types=1);

namespace App\Enum;

enum MessageState: string
{
    case READ = 'READ';
    case UNREAD = 'UNREAD';
}