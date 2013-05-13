<?php


namespace AlanPich\Modx\CLI;

use \Symfony\Component\Console\Input\InputInterface;

class Application extends \Symfony\Component\Console\Application
{
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
        $path = MODX_CLI_TOOL.'commands/';
        $dh = opendir($path);
        while (false !== ($f = readdir($dh))) {
            if($f=='.'||$f=='..') continue;
            $info = pathinfo($f);
            if($info['extension']!='php') continue;
            if($info['filename'])
            require $path.$f;
            $class = $info['filename'];
            $defaultCommands[] = new $class;
        }

        return $defaultCommands;
    }
}
