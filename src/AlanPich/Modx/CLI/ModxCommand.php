<?php

namespace AlanPich\Modx\CLI;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AlanPich\Modx\CLI\Configuration;


class ModxCommand extends AnnotatedCommand
{
    /** @var Configuration */
    protected $config;

    /** @var \modX */
    protected $modx;

    /**
     * Set up
     */
    protected function configure()
    {
        parent::configure();

        // Load configuration
        $this->config = new Configuration( getcwd().DIRECTORY_SEPARATOR.'modx-cli.json');

        // Load MODx instance
        $this->modx = $this->config->getModx();
    }

}
