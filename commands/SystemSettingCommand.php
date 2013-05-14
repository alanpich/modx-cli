<?php
use AlanPich\Modx\CLI\ModxProcessorCommand;
use AlanPich\Modx\Installer\ModxInstallerConfig;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Clears the MODx cache
 *
 * Clears the MODx cache of an installation specified by --path
 *
 * @command clear-cache
 */
class ClearCacheCommand extends ModxProcessorCommand {

    protected $processor = 'system/clearcache';
}