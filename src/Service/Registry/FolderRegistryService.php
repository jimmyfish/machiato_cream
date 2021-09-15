<?php

namespace App\Service\Registry;

use Symfony\Component\HttpKernel\KernelInterface;

class FolderRegistryService
{
    private $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }
    
    protected function getTemporaryDir()
    {
        return $this->appKernel->getProjectDir() . "/var/cache";
    }

    protected function getInputDir()
    {
        return $this->getTemporaryDir() . "/inputs";
    }
}