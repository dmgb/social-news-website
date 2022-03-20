<?php declare(strict_types=1);

namespace App\Helper;

use DateTimeInterface;

class DateTimeFormatter
{
    public static function timeAgo(DateTimeInterface $date): string|null
    {
        $time = time() - $date->getTimestamp();
        if ($time === 0) {
            return 'just now';
        }

        $units = [
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second',
        ];

        foreach ($units as $unit => $val) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);

            return ($val === 'second') ? 'a few seconds ago' :
                (($numberOfUnits > 1) ? $numberOfUnits : 'a'). ' ' .$val.(($numberOfUnits > 1) ? 's' : ''). ' ago';
        }

        return null;
    }
}
