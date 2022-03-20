<?php declare(strict_types=1);

namespace App\Twig;

use App\Helper\DateTimeFormatter;
use DateTimeInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function getTimeAgo(DateTimeInterface $date): string|null
    {
        return DateTimeFormatter::timeAgo($date);
    }
}
