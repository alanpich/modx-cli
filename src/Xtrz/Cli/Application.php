<?php


namespace Xtrz\Cli;

use Xtrz\Cli\Core\Configuration;
use Xtrz\Modx\Pkg;


class Application extends \Symfony\Component\Console\Application
{

    protected $config;

    /** @var  \Xtrz\Modx\Pkg */
    protected $pkg;

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

        // Load global config
        $this->getConfig();

        foreach ($this->config->includes as $dir) {
            $defaultCommands = $this->includeDirectory($defaultCommands, $dir);
        };

        return $defaultCommands;
    }


    /**
     * Get the package object
     *
     * @return \Xtrz\Modx\Pkg
     */
    public function getPkg(){
        if(!$this->pkg){
            // Where to look
            $path = getcwd() . '/xtrz.json';
            if(is_readable($path)){
                $this->pkg = Pkg::fromFile($path);
            }
        }
        return $this->pkg;
    }


    public function getConfig()
    {
        if (is_null($this->config)) {
            $this->config = Configuration::getInstance(getcwd() . DIRECTORY_SEPARATOR . 'modx-cli.json');

     //       $this->config->includes[] = dirname(__DIR__) . '/Command';

        }
        return $this->config;
    }

    /**
     * @param $defaultCommands
     * @param $path
     * @return array
     */
    protected function includeDirectory($defaultCommands, $path)
    {
        // Enforce trailing slash on path
        $path = rtrim($path,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        // Check for a 'Command.php' file first - bootstrap libs here
        $file = $path . 'Command.php';
        if (is_readable($file)) {
            $this->registerCommandFile($defaultCommands,$file);
        }


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


    /**
     * Register a file as a command
     *
     * @param $defaultCommands
     * @param string $path
     * @return array
     */
    protected function registerCommandFile($defaultCommands, $path)
    {
        $info = pathinfo($path);
        if ($info['extension'] != 'php') {
            return $defaultCommands;
        }
        $className = $info['filename'];
        require $path;

        $defaultCommands[] = new $className;

        return $defaultCommands;
    }

}
