<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class MigrateToCSVTest extends KernelTestCase
{
    private $source = "https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl";

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var Application $application */
    private $application;

    private $dataMigratorCommand;
    private $commandTester;

    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
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
        $output = str_replace("\r", "", str_replace("\n", "", $output));
        $directory = explode(" ", $output);

        $this->assertFileExists($directory[2]);
    }

    public function get_data_and_convert_it_into_csv_and_store_it_to_db(): void
    {
        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
