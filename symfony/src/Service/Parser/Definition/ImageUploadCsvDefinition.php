<?php

namespace App\Service\Parser\Definition;

use App\Service\Parser\Normalizer\Value\NormalizerRules;
use App\Service\Parser\Validator\Value\ValidationRules;

class ImageUploadCsvDefinition extends CsvDefinition
{
    /**
     * @return array
     */
    public function getDefinition(): array
    {
        return [
            'picture_title' => [
                'validation' => [ValidationRules::MANDATORY],
                'normalizer' => [NormalizerRules::TRIM]
            ],
            'picture_url' => [
                'validation' => [ValidationRules::URL],
                'normalizer' => [NormalizerRules::TRIM]
            ],
            'picture_description' => [
                'normalizer' => [NormalizerRules::TRIM]
            ]
        ];
    }
}