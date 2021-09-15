<?php

namespace App\Service\Data;

use App\Service\Registry\FolderRegistryService;
use App\Service\Requester\FetchDataService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class DataProcessorService extends FolderRegistryService
{
    private $fetchDataService;
    public function __construct(
        FetchDataService $fetchDataService,
        KernelInterface $kernelInterface
    ) {
        parent::__construct($kernelInterface);
        $this->fetchDataService = $fetchDataService;
    }

    public function csv(string $src, bool $db = false)
    {
        $fetcher = $this->fetchDataService->fetch($src);
        $filename = $this->getFullPath($fetcher['filenameWithExtension']);

        dump($filename);
    }

    public function yaml(string $src)
    {
        return "This gonna be YAML Side";
    }

    public function getFullPath(string $input)
    {
        return $this->getInputDir() . "/$input";
    }
}
