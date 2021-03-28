<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpWebsiteCommand extends Command
{
    protected static $defaultName = 'up';
    protected static $defaultDescription = 'Set back the website under functionment';

    public function __construct($maintenance)
    {
        $this->maintenanceFilePath = $maintenance;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (\file_exists($this->maintenanceFilePath)) {
            unlink($this->maintenanceFilePath);
        }

        $io->success('Your website is now under functionment.');

        return Command::SUCCESS;
    }
}
