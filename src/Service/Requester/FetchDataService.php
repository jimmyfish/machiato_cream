<?php

namespace App\Service\Requester;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use App\Service\Registry\FolderRegistryService;
use Symfony\Component\HttpKernel\KernelInterface;

class FetchDataService extends FolderRegistryService
{
    public function fetch(string $src)
    {
        $client = HttpClient::create();

        $response = $client->request('GET', $src);

        if ($response->getStatusCode() !== 200) throw new Exception('GTFO');
    }

    public function saveCache($input)
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($this->getInputDir())) {
            $filesystem->mkdir($this->getInputDir());
        }

        $filename = md5(uniqid()) . ".jsonl";
    }
}