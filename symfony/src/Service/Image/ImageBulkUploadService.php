<?php

namespace App\Service\Image;

use App\Service\Parser\Builder\CsvUploadBuilder;
use App\Service\Parser\Definition\ImageUploadCsvDefinition;
use App\Service\Parser\Factory\CsvFileFactory;
use App\Service\Parser\Validator\ImageUploadCsvValidator;
use SplFileInfo;
use LogicException;
use Doctrine\ORM\NonUniqueResultException;

class ImageBulkUploadService
{
    /** @var CsvUploadBuilder */
    private $fileUploadBuilder;

    /** @var ImageUploadCsvValidator */
    private $fileValidator;

    /** @var ImageUploadCsvDefinition */
    private $fileDefinition;

    /** @var CsvFileFactory */
    private $fileFactory;

    /**
     * ImageUploadService constructor.
     * @param CsvUploadBuilder $fileUploadBuilder
     * @param ImageUploadCsvValidator $fileValidator
     * @param ImageUploadCsvDefinition $fileDefinition
     * @param CsvFileFactory $fileFactory
     */
    public function __construct(
        CsvUploadBuilder $fileUploadBuilder,
        ImageUploadCsvValidator $fileValidator,
        ImageUploadCsvDefinition $fileDefinition,
        CsvFileFactory $fileFactory
    ) {
        $this->fileUploadBuilder = $fileUploadBuilder;
        $this->fileValidator = $fileValidator;
        $this->fileDefinition = $fileDefinition;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @param SplFileInfo|null $file
     * @throws NonUniqueResultException
     */
    public function csvBulkUpload(?SplFileInfo $file): void
    {
        if ($file === null) {
            throw new LogicException('Expected file, gut null');
        }

        $fileValidator  = clone $this->fileValidator;
        $fileFactory    = clone $this->fileFactory;
        $fileDefinition = clone $this->fileDefinition;
        $uploader       = clone $this->fileUploadBuilder;

        $uploader
            ->addFile($file, CsvFileFactory::TYPE_IMAGE_UPLOAD_CSV_FILE)
            ->addValidator($fileValidator)
            ->addFactory($fileFactory)
            ->addDefinition($fileDefinition)
        ->build();
    }
}
