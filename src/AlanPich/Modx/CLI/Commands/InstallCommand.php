<?php

namespace AlanPich\Modx\CLI\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AlanPich\Modx\CLI\AbstractCommand;


class InstallCommand extends AbstractCommand {


    protected function configure()
    {
        // Global globals options
        parent::configure();

        $this
            ->setName('install')
            ->setDescription('Create a new MODx installation at the specified path')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Installation Path'
            );


        // Database Name
        $this->addOption('dbname',
            null,
            InputOption::VALUE_REQUIRED,
            'Database name');

        // Database User
        $this->addOption('dbuser',
            null,
            InputOption::VALUE_REQUIRED,
            'Database user');

        // Database password
        $this->addOption('dbpass',
            null,
            InputOption::VALUE_REQUIRED,
            'Database password');



        // Web server hostname
        $this->addOption('http_host',
            null,
            InputOption::VALUE_REQUIRED,
            'Http server hostname');

        // Web URL
        $this->addOption('site_url',
            null,
            InputOption::VALUE_REQUIRED,
            'Site URL path');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);


        $dbname = $input->getOption('dbname');



        $db_name = $this->required('dbname',$input,$output);


        die("DBNAME: $db_name\n");


        $path = $input->getArgument('path');

        $dialog = $this->getHelperSet()->get('dialog');

        $db_name = $input->getOption("dbname");

        $version = $dialog->ask(
                $output,
                "Database Name: "
            );

        $output->writeln("Installing MODx <info>v{$version}</info> at <info>{$path}</info> with database <info>{$db_name}</info>");



    }



}
