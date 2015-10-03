<?php

namespace Benchy;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BenchmarkCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('benchmark')
            ->setDescription('run the benchmark')
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                'The config file'
            )
            /*
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_OPTIONAL,
                'The output directory'
            )
            */
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //var_dump($input->getOption('output'));

        $configfile = $this->findConfigurationFile($input);
        if (null === $configfile) {
            $output->writeln('no config file found.');
            return 1;
        }


        $datadir = getcwd() . '/benchy/data/';
        if (!file_exists($datadir)) {
            mkdir($datadir, 0777, true);
        }

        define('DATA_DIR', $datadir);

        require __DIR__ . '/../benchy.php';

        return 0;
    }

    /**
     * @param InputInterface $input
     * @return string|null
     */
    private function findConfigurationFile(InputInterface $input)
    {
        $configfiles = array();

        if (null !== $input->getOption('config')) {
            $configfiles[] = $input->getOption('config');
        } else {
            $configfiles[] = getcwd() . '/benchy.json';
            $configfiles[] = getcwd() . '/config.json';
        }

        $configfile = null;
        foreach ($configfiles as $file) {
            if (file_exists($file)) {
                define('CONFIG_FILE', $file);
                return $file;
            }
        }

        return null;
    }
}
