<?php

namespace App\Command;

use App\Software\DataCollector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Finder\Finder;
use App\Software\Client;

class ImportDataCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    protected static $defaultName = 'app:import:data';

    private $collector;

    public function __construct(
        DataCollector $collector,
        Client $client
    ) {
        $this->collector = $collector;
        $this->client = $client;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('filename', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Importation de données du fichier {$input}");

        $finder = new Finder();
        $path_files_csv = $this->container->getParameter('path_files_csv');
        $finder->files()->in($path_files_csv);
        foreach ($finder as $file) {
            $filename = $path_files_csv.DIRECTORY_SEPARATOR.$file->getRelativePathname();
            if (!file_exists($filename) || !is_readable($filename)) {
                $io->error(sprintf('The provided filename "%s" is not readable!', $filename));
                continue;
            }

            $this->collector->addFile($filename);
        }
        $all_data = $this->collector->collectDataCsv();
        $encoded_data = json_encode($all_data);
        $io->text($encoded_data);
        $res = $this->client->put('https://api.display-interactive.com/v1/customers', $encoded_data);
        $io->note('Status result PUT : '.$res->getStatusCode());
        $io->success('Done !');
        $io->text($encoded_data);
    }
}
