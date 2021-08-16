<?php

namespace App\Service\Parser\Validator;

use App\Service\Parser\Definition\CsvDefinition;
use App\Service\Parser\Entity\CsvFile;
use Exception;

abstract class CsvValidator extends Validator
{
    /**
     * @param CsvFile $file
     * @param CsvDefinition $csvDefinition
     * @throws Exception
     */
    final public function validateOrThrow(CsvFile $file, CsvDefinition $csvDefinition): void
    {
        $csvBodyRows = $file->getRawBody();
        $csvHeaderValidationRules = $csvDefinition->getHeaderValidationRules();

        foreach ($csvBodyRows as $index => $fieldValues) {
            foreach ($fieldValues as $index2 => $value) {
                $validationRules = $csvHeaderValidationRules[$index2] ?? [];
                if (!is_array($validationRules) || empty($validationRules)) {
                    continue;
                }
                $value = is_string($value) ? $value : null;

                foreach ($validationRules as $validationRule) {
                    if (!$this->isValid($value, $validationRule)) {
                        $message = sprintf(
                            'Validation failed when checking csv row %s, column %s, rule %s',
                            $index + 2, //start cell numbering from 1, consider header row
                            $index2 + 1, //start cell numbering from 1
                            $validationRule
                        );
                        throw new Exception($message);
                    }
                }
            }
        }
    }
}
