<?php

namespace App\Service\Parser\Normalizer;

use App\Service\Parser\Normalizer\Value\NormalizerRules;

class Normalizer
{
    /**
     * @param string|null $value
     * @param string $rule
     */
    final public static function normalize(?string &$value, string $rule): void
    {
        if ($rule === NormalizerRules::TRIM) {
            $value = self::addTrim($value);
        }
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    private static function addTrim(?string $value): ?string
    {
        return isset($value) ? trim($value) : $value;
    }
}
