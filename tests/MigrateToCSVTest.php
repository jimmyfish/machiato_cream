<?php

namespace App\Tests;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class MigrateToCSVTest extends KernelTestCase
{
    private $source = "https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl";

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    private $doctrine;

    /** @var Application $application */
    private $application;

    private $dataMigratorCommand;
    private $commandTester;

    /**
     * Rise and shine
     */
    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->doctrine = $kernel->getContainer()->get('doctrine');
        $this->application = new Application($kernel);
        $this->dataMigratorCommand = $this->application->find('data:migrate');
        $this->commandTester = new CommandTester($this->dataMigratorCommand);
    }

    /** @test drunken_user */
    public function avoid_drunken_user_from_provoking_the_app_by_typing_wrong_filetype(): void
    {
        $this->commandTester->execute([
            'src' => $this->source,
            '--filetype' => 'csc'
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('not supported', $output);
    }

    /** @test migrate data to csv */
    public function get_data_and_convert_it_into_csv(): void
    {
        $this->commandTester->execute([
            'src' => $this->source,
            '--filetype' => 'csv'
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('OK', $output);

        # If file created and validated
        $output = preg_replace('/\s+/', '', $output);
        $directory = explode("]", $output);

        $this->assertFileExists($directory[1]);
    }

    /** @test migrate data to yaml */
    public function get_data_and_convert_it_into_yaml(): void
    {
        $this->commandTester->execute([
            'src' => $this->source,
            '--filetype' => 'yaml'
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('OK', $output);

        # If file created and validated
        $output = preg_replace('/\s+/', '', $output);
        $directory = explode("]", $output);

        $this->assertFileExists($directory[1]);
    }

    /** @test convert into csv and store it into db */
    public function get_data_and_convert_it_into_csv_and_store_it_to_db(): void
    {
        $this->commandTester->execute([
            'src' => $this->source,
            '--filetype' => 'csv',
            '--db' => true,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('OK', $output);

        # If file created and validated
        $output = preg_replace('/\s+/', '', $output);
        $arrayOutput = explode(":", $output);

        $hash = $arrayOutput[1];

        $data = $this->entityManager->getRepository(Order::class)->countDataUsingHash($hash);

        $this->assertGreaterThan(0, $data);
    }

    /** @test migrate data to yaml */
    public function get_data_and_convert_it_into_yaml_and_store_it_to_db(): void
    {
        $this->commandTester->execute([
            'src' => $this->source,
            '--filetype' => 'yaml',
            '--db' => true,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('OK', $output);

        # If file created and validated
        $output = preg_replace('/\s+/', '', $output);
        $arrayOutput = explode(":", $output);

        $hash = $arrayOutput[1];

        $data = $this->entityManager->getRepository(Order::class)->countDataUsingHash($hash);

        $this->assertGreaterThan(0, $data);
    }

    /**
     * Cleaning and self-destruct
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
