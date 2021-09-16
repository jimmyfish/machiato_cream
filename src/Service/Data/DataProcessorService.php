<?php

namespace App\Service\Data;

use DateTime;
use App\Entity\Order;
use App\Service\Mailer\SendEmailService;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
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
    private $em;
    private $sendEmailService;

    public function __construct(
        FetchDataService $fetchDataService,
        KernelInterface $kernelInterface,
        DataManipulationService $dataManipulationService,
        Filesystem $filesystem,
        EntityManagerInterface $em,
        SendEmailService $sendEmailService
    ) {
        parent::__construct($kernelInterface);
        $this->fetchDataService = $fetchDataService;
        $this->dataManipulationService = $dataManipulationService;
        $this->filesystem = $filesystem;
        $this->em = $em;
        $this->sendEmailService = $sendEmailService;

        if (!$this->filesystem->exists($this->getOutputPath())) $this->filesystem->mkdir($this->getOutputPath());
    }

    public function csv(string $src, bool $db = false, $email = null)
    {
        $fetcher = $this->fetchDataService->fetch($src);
        $filename = $this->getFullPath($fetcher['filenameWithExtension']);
        $outputFile = $this->getOutputPath($fetcher['filename'] . ".csv");

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

        if ($db) $this->insertIntoDB($csvItems, $fetcher['filename']);

        if ($email) $this->sendEmailService->send($email, "", [], "", $fetcher['filenameWithExtension']);

        return $db ? "Batch number : " . $fetcher['filename'] : $outputFile;
    }

    public function yaml(string $src, bool $db = false, $email = null)
    {
        $fetcher = $this->fetchDataService->fetch($src);
        $filename = $this->getFullPath($fetcher['filenameWithExtension']);
        $outputFile = $this->getOutputPath($fetcher['filename'] . ".yaml");

        $result = $this->dataManipulationService->getData($filename);

        $yaml = Yaml::dump($result);

        file_put_contents($outputFile, $yaml);

        if ($db) $this->insertIntoDB($result, $fetcher['filename']);

        if ($email) $this->sendEmailService->send($email, "", [], "", $fetcher['filenameWithExtension']);

        return $db ? "Batch number : " . $fetcher['filename'] : $outputFile;
    }

    public function getFullPath(string $input)
    {
        return $this->getInputDir() . "/$input";
    }

    public function insertIntoDB($data, $batchNumber)
    {
        foreach ($data as $datum) {
            $collections = new Order();
            $collections->setOrderId($datum['order_id']);
            $collections->setOrderDatetime(new DateTime($datum['order_datetime']));
            $collections->setTotalOrderValue($datum['total_order_value']);
            $collections->setAverageUnitPrice($datum['average_unit_price']);
            $collections->setDistinctUnitCount($datum['distinct_unit_count']);
            $collections->setTotalUnitsCount($datum['total_unit_count']);
            $collections->setCustomerState($datum['customer_state']);
            $collections->setBatchNumber($batchNumber);

            $this->em->persist($collections);
        }

        $this->em->flush();

        return $batchNumber;
    }
}
