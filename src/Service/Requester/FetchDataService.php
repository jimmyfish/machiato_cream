<?php

namespace App\Service\Requester;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use App\Service\Registry\FolderRegistryService;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FetchDataService extends FolderRegistryService
{
    public function fetch(string $src)
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($this->getInputDir())) {
            $filesystem->mkdir($this->getInputDir());
        }
        
        $fileHandler = null;

        try {
            $client = HttpClient::create();

            $response = $client->request('GET', $src);

            if ($response->getStatusCode() !== 200) throw new Exception('GTFO');

            $filename = md5(uniqid()) . ".jsonl";

            $fileHandler = fopen($this->getInputDir() . "/" . $filename, "w+");

            foreach ($client->stream($response) as $chunk) {
                fwrite($fileHandler, $chunk->getContent());
            }
        } catch (TransportExceptionInterface $exception) {
            return new JsonResponse(["message" => $exception->getMessage()]);
        } catch (Exception $exception) {
            return new JsonResponse(["message" => $exception->getMessage()]);
        }

        return $fileHandler;
    }
}
