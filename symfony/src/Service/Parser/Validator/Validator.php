<?php

namespace App\Service\Parser\Validator;

use App\Service\Parser\Validator\Value\ValidationRules;

abstract class Validator
{
    /**
     * @param string|null $value
     * @param string $rule
     * @return bool
     */
    final protected function isValid(?string $value, string $rule): bool
    {
        if ($rule === ValidationRules::MANDATORY) {
            return $this->checkMandatory($value);
        }
        if ($rule === ValidationRules::URL) {
            return $this->checkUrl($value);
        }
        return true;
    }

    /**
     * @param $value
     * @return bool
     */
    private function checkMandatory(?string $value): bool
    {
        return !filter_var(trim($value), FILTER_FLAG_EMPTY_STRING_NULL) === false;
    }

    /**
     * @param string|null $value
     * @return bool
     */
    private function checkUrl(?string $value): bool
    {
        return !filter_var(trim($value), FILTER_VALIDATE_URL) === false;
    }
}
