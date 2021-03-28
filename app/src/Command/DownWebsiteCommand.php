<?php

namespace App\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DownWebsiteCommand extends Command
{
    private $maintenanceFilePath;

    protected static $defaultName = 'down';
    protected static $defaultDescription = 'Place the website under maintenance mode';

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

        $handler = \fopen($this->maintenanceFilePath, 'a+');
        if ($handler === false) {
            throw new RuntimeException('File ' . $this->maintenanceFilePath . ' could not be created', 500);
        }
        \fclose($handler);

        $io->success('Your website is now under maintenance.');

        return Command::SUCCESS;
    }
}
