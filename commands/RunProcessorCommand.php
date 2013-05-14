<?php

use AlanPich\Modx\CLI\ModxCommand;
use AlanPich\Modx\Installer\ModxInstallerConfig;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Runs a MODx Processor script
 *
 * Call a MODx processor directly from the command line.
 * Script properties should be passed in the form of --key=value
 *
 * @command run-processor
 */
class RunProcessorCommand extends ModxCommand
{

    protected function defineCommandOptions(){

        $this->addArgument('processor',InputArgument::REQUIRED);

    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);

        $processorName = $input->getArgument('processor');

        $output->writeln("<info>Running processor {$processorName}</info>");


        $response = $this->modx->runProcessor($processorName,array());

       # print_r(array_keys(get_object_vars($response)));
        $output->write(print_r($response,1));
    }
}
