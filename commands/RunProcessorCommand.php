<?php

use AlanPich\Modx\CLI\AnnotatedCommand;
use AlanPich\Modx\Installer\ModxInstallerConfig;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Handles global configuration settings
 *
 * Allows setting of global configuration options that
 * will be used to by other available commands
 *
 * @command globals
 */
class ConfigCommand extends AnnotatedCommand
{

    protected function defineCommandOptions()
    {

        // Flag to show all globals options
        $this->addOption('all', 'A', InputOption::VALUE_NONE, "List all defined globals settings");

        $this->addArgument('key', InputArgument::OPTIONAL, "Config param name", null);
        $this->addArgument('value', InputArgument::OPTIONAL, "If set, will set param [key] to this value", null);

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('all')) {
            return $this->listAllOptions($input, $output);
        }

        $key = $input->getArgument('key');
        if (is_null($key)) {
            return;
        }

        $value = $input->getArgument('value');

        if (is_null($value)) {
            $this->showConfigKey($key, $input, $output);
        } else {
            $this->setConfigKey($key, $value, $input,$output);
        }

    }


    /**
     * Displays a list of all defined globals settings
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function listAllOptions(InputInterface $input, OutputInterface $output)
    {
        $settings = $this->globals->toArray();
        $table = $this->getHelperSet()->get('table');

        $table->setHeaders(array('Key','Value'));

        foreach($settings as $key => $val){
            $rows[] = array($key,$val);
        }
        $table->setRows($rows);
        $table->render($output);
        return 0;
    }


    /**
     * Display the value of a globals setting
     *
     * @param string                                           $key
     * @param Symfony\Component\Console\Input\InputInterface   $input
     * @param Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function showConfigKey($key, InputInterface $input, OutputInterface $output)
    {
        $output->writeln("$key - // @TODO Show value here");
    }


    /**
     * @param string           $key
     * @param mixed            $value
     * @param InputInterface   $input
     * @param OutputInterface  $output
     */
    protected function setConfigKey($key, $value, InputInterface $input, OutputInterface $output)
    {
        $this->globals->$key = $value;
    }


}
