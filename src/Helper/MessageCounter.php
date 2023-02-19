<?php declare(strict_types=1);

namespace App\Helper;

use App\Entity\Message;
use App\Entity\User;
use App\Enum\MessageState;

class MessageCounter
{
    public static function count(User $user): int
    {
        return $user->getReceivedMessages()
            ->filter(fn (Message $message) => $message->getState() === MessageState::UNREAD)
            ->count();
    }
}
