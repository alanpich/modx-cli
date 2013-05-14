<?php

namespace AlanPich\Modx\CLI;
use AlanPich\Modx\CLI\ModxCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


abstract class ModxProcessorCommand extends ModxCommand
{
    protected $processor;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);


        $output->writeln("<info>Running processor {$this->processor}</info>");


        $response = $this->modx->runProcessor($this->processor);

        # print_r(array_keys(get_object_vars($response)));
        $output->write(print_r($response,1));
    }

}