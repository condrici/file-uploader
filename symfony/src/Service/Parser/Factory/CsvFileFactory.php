<?php

namespace App\Service\Parser\Factory;

use App\Service\Parser\Entity\CsvFile;
use App\Service\Parser\Entity\ImageUploadCsvFile;
use App\Service\Parser\Value\AllowedMimeTypes;
use SplFileInfo;
use Exception;
use Throwable;
use InvalidArgumentException;
use LogicException;

class CsvFileFactory
{
    /** @var array  */
    private const ALLOWED_MIME_TYPES = AllowedMimeTypes::TYPE_CSV;

    /** @var string  */
    public const TYPE_IMAGE_UPLOAD_CSV_FILE = 'image_upload_csv_file';

    /**
     * @param SplFileInfo|null $file
     * @param string $type
     * @return CsvFile
     * @throws Exception
     */
    public function create(?SplFileInfo $file, string $type): CsvFile
    {
        try {
            $this->validateOrThrow($file);
            if ($type === self::TYPE_IMAGE_UPLOAD_CSV_FILE) {
                return new ImageUploadCsvFile($file);
            }
            throw new LogicException('Unknown csv type ' . $type);
        } catch (Throwable $exception) {
            throw new Exception('Could not parse file: ' . $exception->getMessage());
        }
    }

    /**
     * @param SplFileInfo|null $file
     * @throws Exception
     */
    private function validateOrThrow(?SplFileInfo $file): void
    {
        if ($file === null) {
            throw new InvalidArgumentException('Not a file');
        }
        $openedFile = fopen($file, 'rb');
        if ($openedFile === false) {
            throw new Exception('Could not read from file');
        }
        if (!is_resource($openedFile)) {
            throw new Exception('Not a resource file');
        }
        if (get_resource_type($openedFile) !== 'stream') {
            throw new Exception('Expected stream resource, got ' . get_resource_type($openedFile));
        }
        fclose($openedFile);
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES, true)) {
            throw new Exception('Unknown csv mime type ' . $file->getMimeType());
        }
    }
}
