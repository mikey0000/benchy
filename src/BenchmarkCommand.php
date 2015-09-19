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
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_OPTIONAL,
                'The output directory'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //var_dump($input->getOption('config'));
        //var_dump($input->getOption('output'));

        require 'benchy.php';
    }
}