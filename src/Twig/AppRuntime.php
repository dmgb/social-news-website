<?php declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use App\Helper\DateTimeFormatter;
use App\Helper\MessageCounter;
use DateTimeInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function getTimeAgo(DateTimeInterface $date): string|null
    {
        return DateTimeFormatter::timeAgo($date);
    }

    public function getMessageCount(User $user): int
    {
        return MessageCounter::count($user);
    }
}
