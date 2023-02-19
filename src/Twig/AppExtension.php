<?php declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', [AppRuntime::class, 'getTimeAgo']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('message_count', [AppRuntime::class, 'getMessageCount']),
        ];
    }
}
