<?php

use AlanPich\Modx\CLI\AnnotatedCommand;
use AlanPich\Modx\Installer\ModxInstallerConfig;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Creates a new MODx installation at the desired path
 *
 * This command will install the latest version of MODx to
 * the path specified by the first argument. Configuration can
 * be passed as options, loaded from a file, or set using the
 * interactive command mode.
 *
 * @command install
 */
class InstallCommand extends AnnotatedCommand
{

    /** @var bool When running interactively, prompt for all globals options */
    public $allPrompts = false;


    /**
     * Define command options and arguments
     *
     */
    protected function defineCommandOptions()
    {
        $this->addArgument(
            'path',
            InputArgument::REQUIRED,
            'Installation Path'
        );

        /**
         * Allows user to supply path to a globals file to use
         */
        $this->addOption(
            'import-globals',
            'i',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Import settings from globals file',
            array()
        );

        /**
         * If running iteractively, prompt for all available options
         */
        $this->addOption(
            'all',
            'A',
            InputOption::VALUE_NONE,
            'Prompt for all globals options when running interactively'
        );


        /**
         * ModxInstallerConfig properties
         */
        $this->addOption('database_type', null, InputOption::VALUE_REQUIRED, 'DB Type');
        $this->addOption('database_server', null, InputOption::VALUE_REQUIRED, 'DB Host');
        $this->addOption('database', null, InputOption::VALUE_REQUIRED, 'DB Name');
        $this->addOption('database_user', null, InputOption::VALUE_REQUIRED, 'DB User');
        $this->addOption('database_password', null, InputOption::VALUE_REQUIRED, 'DB Password');
        $this->addOption('database_connection_charset', null, InputOption::VALUE_REQUIRED, 'DB Connection charset');
        $this->addOption('database_charset', null, InputOption::VALUE_REQUIRED, 'DB charset');
        $this->addOption('database_collation', null, InputOption::VALUE_REQUIRED, 'DB collation');
        $this->addOption('table_prefix', null, InputOption::VALUE_REQUIRED, 'Table prefix');
        $this->addOption('https_port', null, InputOption::VALUE_REQUIRED, 'Https port');
        $this->addOption('http_host', null, InputOption::VALUE_REQUIRED, 'Http Server host');
        $this->addOption('cache_disabled', null, InputOption::VALUE_REQUIRED, 'Disable cache');
        $this->addOption('inplace', null, InputOption::VALUE_NONE, 'Extracted from GIT?');
        $this->addOption('unpacked', null, InputOption::VALUE_NONE, 'Core package already extracted');
        $this->addOption('language', null, InputOption::VALUE_REQUIRED, 'Default language');
        $this->addOption('cmsadmin', null, InputOption::VALUE_REQUIRED, 'Admin user');
        $this->addOption('cmspassword', null, InputOption::VALUE_REQUIRED, 'Admin password');
        $this->addOption('cmsadminemail', null, InputOption::VALUE_REQUIRED, 'Admin email');
        $this->addOption('core_path', null, InputOption::VALUE_REQUIRED, 'Core Path');
        $this->addOption('context_mgr_path', null, InputOption::VALUE_REQUIRED, 'Manager Path');
        $this->addOption('context_mgr_url', null, InputOption::VALUE_REQUIRED, 'Manager URL');
        $this->addOption('context_connectors_path', null, InputOption::VALUE_REQUIRED, 'Connectors Path');
        $this->addOption('context_connectors_url', null, InputOption::VALUE_REQUIRED, 'Connectors URL');
        $this->addOption('context_web_path', null, InputOption::VALUE_REQUIRED, 'Site Base path');
        $this->addOption('context_web_url', null, InputOption::VALUE_REQUIRED, 'Site Base URL');
        $this->addOption(
            'remove_setup_directory',
            null,
            InputOption::VALUE_NONE,
            "Remove setup directory after install"
        );

    }


    /**
     * Execute the command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws Exception
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Detect flag to prompt for all globals options
        $this->allConfigPrompts = $input->getOption('all');

        // Are we running in interactive mode?
        $interactive = !$input->getOption('no-interaction');


        $config = new ModxInstallerConfig();


        // Load any configs from globals
        foreach($config as $key => $val){
            $configKey = "install.".$key;
            if(!is_null($this->globals->$configKey)){
                $config->$key = $this->globals->$configKey;
            }
        }


        // Are there any globals files to import?
        $configFiles = $input->getOption('import-globals');
        if (count($configFiles)) {
            foreach ($configFiles as $configFile) {
           #     $config = $this->loadConfigFromFile($configFile);
            }
        }



        // Populate globals from inputs
        foreach($config as $key => $val){
            try {
                $value = $input->getOption($key);
                if(!is_null($value)){
                    $config->$key = $value;
                }
            } catch (\Exception $E){
                throw new \Exception("InstallCommand is missing option for $key",$E);
            }
        }



        // Pre-populate some fields based on $path
        $installPath = rtrim($input->getArgument('path'), '/') . DIRECTORY_SEPARATOR;
        $config->context_web_path = $installPath;
        $config->context_mgr_path = $installPath . 'manager/';
        $config->context_connectors_path = $installPath . 'connectors/';
        $config->core_path = $installPath . 'core/';


        // Are we running interactively?
        if ($interactive) {

            $output->writeln("<info>MODx Installation configuration options</info>");

            // Prompt for url paths for pre-population
            $url = "/".ltrim(rtrim($this->promptIfNotSet('context_web_url', $input, $output),'/'),'/').'/';
            $config->context_web_url = $url;
            $config->context_mgr_url = $url."manager/";
            $config->context_connectors_url = $url."connectors/";


            $ths =& $this;
            $config->interactivePopulation(
                !$this->allConfigPrompts,
                function ($key, $default) use ($ths, $input, $output) {
                    return $ths->configPrompt($key, $default, $ths, $input, $output);
                }
            );
        }

        $output->writeln(
            "<info>Installing MODx at {$config->context_web_path}</info>"
        );


        $installer = new \AlanPich\Modx\Installer\ModxInstaller();
        $installer->zipDir = MODX_CLI_TOOL.'installers/';
        $installer->install($config);


    }


    protected function loadConfigFromFile($output)
    {
        $output->writeln('<error>loadConfigFromFile not implemented</error>');
    }


    /**
     * Callback for globals population prompts when
     * running in interactive mode. Needs to be public
     * because it's called from a closure
     *
     * @param string          $key
     * @param string          $default
     * @param InstallCommand  $cmd
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function configPrompt($key, $default, InstallCommand $cmd, InputInterface $input, OutputInterface $output)
    {
        // Grab the option from definition
        $def = $cmd->getDefinition();
        $option = $def->getOption($key);

        $prompt = $option->getDescription();
        $prompt .= is_null($default) ? ': ' : " [{$default}]: ";

        /** @var  $dialog */
        $dialog = $cmd->getHelperSet()->get('dialog');

        $value = $dialog->ask(
            $output,
            $prompt,
            $default
        );

        return $value;
    }


    /**
     * Checks if an input option has been set, and prompts user for it if not
     * @param                 $opt
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function promptIfNotSet($opt, InputInterface $input, OutputInterface $output)
    {
        $option = $this->getDefinition()->getOption($opt);

        if(is_null($input->getOption($opt))){
            $prompt = $option->getDescription().": ";
            $dialog = $this->getHelperSet()->get('dialog');
            $value = $dialog->ask($output,$prompt);
            $input->setOption($opt,$value);
        }

        return $input->getOption($opt);
    }


}
