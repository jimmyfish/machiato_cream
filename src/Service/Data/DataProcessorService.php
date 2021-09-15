<?php

namespace App\Service\Data;

use Symfony\Component\Yaml\Yaml;
use App\Service\Requester\FetchDataService;
use Symfony\Component\Filesystem\Filesystem;
use App\Service\Registry\FolderRegistryService;
use Symfony\Component\HttpKernel\KernelInterface;

class DataProcessorService extends FolderRegistryService
{
    private $fetchDataService;
    private $dataManipulationService;
    private $filesystem;
    private $keys = [
        "order_id",
        "order_datetime",
        "total_order_value",
        "average_unit_price",
        "distinct_unit_count",
        "total_units_count",
        "customer_state"
    ];

    public function __construct(
        FetchDataService $fetchDataService,
        KernelInterface $kernelInterface,
        DataManipulationService $dataManipulationService,
        Filesystem $filesystem
    ) {
        parent::__construct($kernelInterface);
        $this->fetchDataService = $fetchDataService;
        $this->dataManipulationService = $dataManipulationService;
        $this->filesystem = $filesystem;

        if (!$this->filesystem->exists($this->getOutputPath())) $this->filesystem->mkdir($this->getOutputPath());
    }

    public function csv(string $src, bool $db = false)
    {
        $fetcher = $this->fetchDataService->fetch($src);
        $filename = $this->getFullPath($fetcher['filenameWithExtension']);
        $outputFile = $this->getOutputPath($fetcher['filename'] . ".csv");

        // if ($this->filesystem->exists($outputFile)) {
        //     return $outputFile;
        // }

        $fileToBeWritten = fopen($outputFile, "w+");

        $csvItems = $this->dataManipulationService->getData($filename);

        fputcsv($fileToBeWritten, $this->keys, ",");

        foreach ($csvItems as $item) {
            $last = end($item);
            foreach ($item as $itm) {
                fwrite($fileToBeWritten, "$itm");
                if ($itm !== $last) {
                    fwrite($fileToBeWritten, ",");
                }
            }
            fwrite($fileToBeWritten, "\n");
        }

        return $outputFile;
    }

    public function yaml(string $src, bool $db = false)
    {
        $fetcher = $this->fetchDataService->fetch($src);
        $filename = $this->getFullPath($fetcher['filenameWithExtension']);
        $outputFile = $this->getOutputPath($fetcher['filename'] . ".yaml");

        $result = $this->dataManipulationService->getData($filename);

        $yaml = Yaml::dump($result);

        file_put_contents($outputFile, $yaml);

        return $outputFile;
    }

    public function getFullPath(string $input)
    {
        return $this->getInputDir() . "/$input";
    }
}
