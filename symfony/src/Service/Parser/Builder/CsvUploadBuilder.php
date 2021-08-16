<?php

namespace App\Service\Parser\Builder;

use App\Service\Image\ImageCrudService;
use App\Service\Image\ImageEventDispatcherService;
use App\Service\Parser\Definition\CsvDefinition;
use App\Service\Parser\Factory\CsvFileFactory;
use App\Service\Parser\Validator\CsvValidator;
use SplFileInfo;
use Exception;

class CsvUploadBuilder
{
    /** @var SplFileInfo */
    private $file;

    /** @var string */
    private $fileType;

    /** @var CsvDefinition */
    private $fileDefinition;

    /** @var CsvFileFactory */
    private $fileFactory;

    /** @var CsvValidator */
    private $fileValidator;

    /** @var ImageCrudService */
    private $imageCrudService;

    /**
     * CsvUploadBuilder constructor.
     * @param ImageCrudService $imageCrudService
     */
    public function __construct(ImageCrudService $imageCrudService)
    {
        $this->imageCrudService = $imageCrudService;
    }

    /**
     * @param SplFileInfo $file
     * @param string $fileType
     * @return $this
     */
    public function addFile(SplFileInfo $file, string $fileType): self
    {
        $this->file = $file;
        $this->fileType = $fileType;
        return $this;
    }

    /**
     * @param CsvDefinition $fileDefinition
     * @return $this
     */
    public function addDefinition(CsvDefinition $fileDefinition): self
    {
        $this->fileDefinition = $fileDefinition;
        return $this;
    }

    /**
     * @param CsvFileFactory $fileFactory
     * @return $this
     */
    public function addFactory(CsvFileFactory $fileFactory): self
    {
        $this->fileFactory = $fileFactory;
        return $this;
    }

    /**
     * @param CsvValidator $fileValidator
     * @return $this
     */
    public function addValidator(CsvValidator $fileValidator): self
    {
        $this->fileValidator = $fileValidator;
        return $this;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function build(): void
    {   
        $this->validateArgumentsOrThrow();
        $csvFile = $this->fileFactory->create($this->file, $this->fileType);
        $this->fileValidator->validateOrThrow($csvFile, $this->fileDefinition);
        $records = $csvFile->getRecordsFromDefinition($this->fileDefinition);
        $this->imageCrudService->createOrUpdate($records, true);
    }

    /**
     * @throws Exception
     */
    private function validateArgumentsOrThrow(): void
    {
        if ($this->file === null) {
            throw new Exception('File cannot be null');
        }
        if ($this->fileType === null) {
            throw new Exception('File type cannot be null');
        }
        if ($this->fileFactory === null) {
            throw new Exception('Factory cannot be null');
        }
        if ($this->fileValidator === null) {
            throw new Exception('File validator cannot be null');
        }
    }
}
