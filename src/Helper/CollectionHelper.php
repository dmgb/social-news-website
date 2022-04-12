<?php declare(strict_types=1);

namespace App\Helper;

use Doctrine\Common\Collections\Collection;

class CollectionHelper
{
    public static function filterIfDoesNotContain(Collection $a, Collection $b): Collection
    {
        return $a->filter(fn($item) => !$b->contains($item));
    }
}
