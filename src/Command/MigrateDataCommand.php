<?php

namespace App\Command;

use App\Service\Data\DataProcessorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateDataCommand extends Command
{
    protected static $defaultName = 'data:migrate';
    protected static $defaultDescription = 'Fetch data from source into file or database';

    private $dataProcessorService;
    private array $supportedFileType = [
        'csv',
        'yaml',
    ];

    public function __construct(DataProcessorService $dataProcessorService)
    {
        parent::__construct();
        $this->dataProcessorService = $dataProcessorService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('src', InputArgument::REQUIRED, 'Set of data needed to be pulled')
            ->addOption('filetype', null, InputArgument::OPTIONAL, 'Specify output file')
            ->addOption('db', null, InputArgument::OPTIONAL, 'Save to DB')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $src = $input->getArgument('src');
        $filetype = $input->getOption('filetype');
        $db = $input->getOption('db') == 'true';

        if (!in_array($filetype, $this->supportedFileType)) {
            $io->warning('Filetype not supported');
            return Command::FAILURE;
        }

        $return = $this->dataProcessorService->{$filetype}($src, $db);

        if (is_string($return)) $io->success($return);

        return Command::SUCCESS;
    }
}
