<?php

namespace App\Service\Parser\Entity;

use App\Service\Parser\Definition\CsvDefinition;
use App\Service\Parser\Normalizer\Normalizer;
use Exception;
use SplFileInfo;
use Throwable;

abstract class CsvFile implements File
{
    /** @var array  */
    private $file;

    /** @var array */
    private $records;

    /** @var string */
    protected $delimiter = ',';

    /**
     * CsvFile constructor.
     * @param SplFileInfo $file
     * @throws Exception
     */
    public function __construct(SplFileInfo $file)
    {
        $this->file = $this->convertFileToArray($file);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->file;
    }

    /**
     * @return array
     */
    public function getRaw(): array
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function getRawHeader()
    {
        return $this->file[0];
    }

    /**
     * @return mixed
     */
    public function getRawBody()
    {
        $raw = $this->file;
        array_shift($raw);
        return $raw;
    }

    /**
     * @param CsvDefinition $csvDefinition
     * @return array
     */
    public function getRecordsFromDefinition(CsvDefinition $csvDefinition): array
    {
        $redefinedHeaderFields = $csvDefinition->getHeaderFields();
        $redefinedBody = $this->getBodyRecordsFromDefinition($csvDefinition);
        $redefinedRecords = [];

        for ($i=0, $iMax = count($redefinedBody); $i< $iMax; $i++) {
            foreach ($redefinedBody as $index1 => $recordSet) {
                foreach ($recordSet as $field => $value) {
                    $index2 = array_search($field, array_keys($recordSet), true);
                    if (!array_key_exists($index2, $redefinedHeaderFields)) {
                        continue;
                    }
                    $redefinedRecords[$index1][$redefinedHeaderFields[$index2]] = (string) $value;
                }
            }
        }

        return json_decode(json_encode($redefinedRecords));
    }

    /**
     * @param SplFileInfo $file
     * @return array
     * @throws Exception
     */
    private function convertFileToArray(SplFileInfo $file): array
    {
        try {
            $csvRows = [];
            $openedFile = fopen($file->getRealPath(), 'rb');
            if ($openedFile === false) {
                throw new Exception('Could not open file!');
            }
            while (!feof($openedFile)) {
                $row = fgetcsv($openedFile, 0, $this->delimiter);
                if (empty($row)) {
                    continue;
                }
                $csvRows[] = $row;
            }
            fclose($openedFile);
        } catch (Throwable $exception) {
            throw new Exception('Could not convert csv to array ' . $exception->getMessage());
        }

        return $csvRows;
    }

    /**
     * @param CsvDefinition $csvDefinition
     * @return array
     */
    private function getBodyRecordsFromDefinition(CsvDefinition $csvDefinition): array
    {
        if (!empty($this->records)) {
            return $this->records;
        }

        $headerFields = $this->file[0];
        $bodyRows = $this->getRawBody();
        $bodyNormalizerRules = $csvDefinition->getBodyNormalizerRules();

        $entries = [];
        for ($i = 0, $iRowMax = count($bodyRows); $i < $iRowMax; $i++) {
            $values = $bodyRows[$i];
            for ($i2 = 0, $iColMax = $iRowMax; $i2 < $iColMax; $i2++) {
                $value = $values[$i2] ?? null;
                if (!isset($value)) {
                    break;
                }
                if (isset($bodyNormalizerRules[$i2])) {
                    foreach ($bodyNormalizerRules[$i2] as $rule) {
                        Normalizer::normalize($value, $rule);
                    }
                }
                $entries[$i][$headerFields[$i2]] = $value;
            }
        }

        $this->records = $entries;
        return $entries;
    }
}
