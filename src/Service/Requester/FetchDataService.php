<?php

namespace App\Service\Requester;

use Exception;
use GuzzleHttp\Client;

class FetchDataService
{
    public function fetch(string $src)
    {
        $client = new Client([
            'headers' => [],
        ]);

        $response = $client->get($src);

        if ($response->getStatusCode() !== 200) throw new Exception('GTFO');
    }

    public function saveCache($input)
    {
    }
}