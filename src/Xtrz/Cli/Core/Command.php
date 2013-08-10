<?php

namespace Xtrz\Cli\Core;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xtrz\Modx\Pkg;


class Command extends AnnotatedCommand
{
    /** @var Configuration Configuration */
    protected $config;
    /** @var \modX */
    protected $modx;

    protected $pkg;

    /**
     * Set up
     */
    public function configure()
    {
        parent::configure();

        // Load configuration
        $this->config = new Configuration(getcwd() . DIRECTORY_SEPARATOR . 'modx-cli.json');

        // Load MODx instance
        $this->modx = $this->config->getModx();
    }


    protected function msg($output,$msg){
        $output->writeln('<info>'.$msg.'</info>');
    }


}
