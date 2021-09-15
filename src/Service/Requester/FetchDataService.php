<?php

namespace App\Service\Requester;

use App\Service\Registry\FolderRegistryService;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;

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

        if (!$filesystem->exists(sys_get_temp_dir() . "/inputs")) {
            $filesystem->mkdir(sys_get_temp_dir() . "/inputs");
        }

        $filename = md5(uniqid());
    }
}