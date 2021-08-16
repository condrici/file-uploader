<?php

namespace App\Service\Parser\Definition;

use LogicException;

abstract class CsvDefinition
{
    /**
     * @return array
     */
    final public function getHeaderFields(): array
    {
        $fields = [];
        $definition = $this->getDefinition();
        foreach ($definition as $key => $definitionSet) {
            $fields[] = is_string($key) ? $key : $definition[$key];
        }

        return $fields;
    }

    /**;
     * @return array
     */
    final public function getHeaderValidationRules(): array
    {
        $rulesSet = [];
        foreach ($this->getDefinition() as $columnName => $definition) {
            if (!is_array($definition)) {
                $rulesSet[] = [];
                continue;
            }
            if (!isset($definition['validation'])) {
                $rulesSet[] = [];
                continue;
            }
            if (!is_array($definition['validation'])) {
                throw new LogicException('Invalid validation definition, expected array');
            }
            $rulesSet[] = $definition['validation'];
        }

        return $rulesSet;
    }

    /**
     * @return array
     */
    final public function getBodyNormalizerRules(): array
    {
        $rulesSet = [];
        foreach ($this->getDefinition() as $columnName => $definition) {
            if (!is_array($definition)) {
                $rulesSet[] = [];
                continue;
            }
            if (!isset($definition['normalizer'])) {
                $rulesSet[] = [];
                continue;
            }
            if (!is_array($definition['normalizer'])) {
                $rulesSet[] = [];
                throw new LogicException('Invalid normalizer definition, expected array');
            }
            $rulesSet[] = $definition['normalizer'];
        }

        return $rulesSet;
    }

    abstract public function getDefinition(): array;
}