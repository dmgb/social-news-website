<?php declare(strict_types=1);

namespace App\Helper;

use Exception;

class RandomStringGenerator
{
    /**
     * @throws Exception
     */
    public static function generate(int $length): string
    {
        $str = '';
        while (strlen($str) < $length) {
            $char = rtrim(base64_encode(random_bytes(1)), '=');
            if (!in_array($char[0], ['+', '/'])) {
                $str .= $char[0];
            }
        }

        return $str;
    }
}
