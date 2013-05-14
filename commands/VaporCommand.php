<?php
use AlanPich\Modx\CLI\ModxCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Creates a Vapor Snapshot of a MODx installation
 *
 * Creates a snapshot for easy transportation and cloning of
 * sites. Can also be used to import into the SiphonCloud
 *
 * @command vapor
 */
class VaporCommand extends ModxCommand {


    protected function defineCommandOptions()
    {
        $this->addArgument('output', InputArgument::OPTIONAL);
    }

    public function execute(InputInterface $input, OutputInterface $output){

        $output->writeln("<info>Vapor-izing MODx installation");

        define('VAPOR_MODX_DIR',$input->getOption('path'));

        include MODX_CLI_TOOL."vendor/vapor/vapor.php";

    }
}