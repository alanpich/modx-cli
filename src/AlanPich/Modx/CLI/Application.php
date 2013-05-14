<?php


namespace AlanPich\Modx\CLI;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;


class Application extends \Symfony\Component\Console\Application
{

    protected $config;

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();

        // Autoload commands
        $path = MODX_CLI_TOOL . 'commands/';
        $dh = opendir($path);
        while (false !== ($f = readdir($dh))) {
            if ($f == '.' || $f == '..') {
                continue;
            }
            $info = pathinfo($f);
            if ($info['extension'] != 'php') {
                continue;
            }
            if ($info['filename']) {
                require $path . $f;
            }
            $class = $info['filename'];
            $defaultCommands[] = new $class;
        }

        return $defaultCommands;
    }


    public function getConfig()
    {
        if (is_null($this->config)) {
            $this->_loadConfig();
        }
        return $this->config;
    }


    protected function _loadConfig()
    {
        $this->config = new Configuration();

        $fileLocations = array(
            // Global config, kept in the MODX_CLI_TOOL/config dir
            MODX_CLI_TOOL . 'config'.DIRECTORY_SEPARATOR.'config.json',
            // User-specific config file kept in home directory
            $_SERVER['HOME'].DIRECTORY_SEPARATOR.'.modx-cli.json',
            // Project-specific config file kept in working directory
            getcwd().DIRECTORY_SEPARATOR.'.modx-cli.json'
        );

        foreach($fileLocations as $f){
            if(is_readable($f))
                $this->config->loadFromFile($f);
        }


    }


}
