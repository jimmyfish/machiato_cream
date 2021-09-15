<?php

namespace App\Command;

use App\Service\Requester\FetchDataService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateDataCommand extends Command
{
    protected static $defaultName = 'data:migrate';
    protected static $defaultDescription = 'Fetch data from source into file or database';

    private $fetchDataService;

    public function __construct(FetchDataService $fetchDataService)
    {
        parent::__construct();
        $this->fetchDataService = $fetchDataService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('src', InputArgument::REQUIRED, 'Set of data needed to be pulled')
            ->addOption('filetype', null, InputArgument::OPTIONAL, 'Specify output file')
            ->addOption('save-to-db', null, InputArgument::OPTIONAL, 'Save to DB')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $src = $input->getArgument('src');

        $this->fetchDataService->fetch($src);

        $io->success('Success!');

        return Command::SUCCESS;
    }
}
