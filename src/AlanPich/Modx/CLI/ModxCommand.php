<?php

namespace AlanPich\Modx\CLI;

use AlanPich\Modx\ModxWrapper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use phpDocumentor\Reflection\DocBlock;


/**
 * Class AbstractCommand
 *
 * @package AlanPich\Modx\CLI
 */
abstract class ModxCommand extends AnnotatedCommand
{

    /** @var \modX  */
    protected $modx;

    protected function configure()
    {
        parent::configure();

        /**
         * Optional argument to explicitely specify path to modx installation
         */
        $this->addOption('path','p',InputOption::VALUE_REQUIRED,'Path to MODx installation');

    }


    /**
     * Define command options and arguments
     */
    protected function defineCommandOptions(){}



    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Establish MODx path
        $path = $input->getOption('path');
        if(is_null($path)){
            $path = $this->checkForConfigFile();
        }

        // Grab a modx wrapper
        $this->modx = new ModxWrapper($path,true);

    }




    protected function checkForConfigFile(){
        $pwd = getcwd();
        echo "Looking for config in $pwd\n";
    }



}
