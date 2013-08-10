<?php
use ModxCLI\Core\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Set a directory config file in the current directory
 *
 * Stores common properties like modx path
 *
 * @command init
 */
class InitCommand extends Command
{

    /**
     * Configure Arguments & Options
     */
    public function configure()
    {
        parent::configure();

        $this->addArgument("modx_path", InputArgument::OPTIONAL, "Path to modx installation to bind to");
        $this->addOption("force","f", InputOption::VALUE_NONE, "Force overwrite of existing config file");

    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @return int|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        if(strlen($this->config->modx_path) && !$input->getOption('force')){
            $output->writeln("<fg=red>Config file already exists in this directory</fg=red>");
            return;
        }
        $modx_path = $input->getArgument('modx_path');


        $output->writeln('Initializing MODx CLI session');

        if (is_null($modx_path)) {
            $modx_path = $dialog->ask(
                $output,
                'Path to MODx installation: '
            );
        };

        $modx_path = rtrim($modx_path);

        if (!is_dir($modx_path))
            throw new Exception('Path not found');

        // Check there is a modx instance
        if (!$this->containsModxInstance($modx_path))
            throw new Exception("MODx installation not found at path");


        $config = new ModxCLI\Core\Configuration(getcwd().DIRECTORY_SEPARATOR.'modx-cli.json');

        $config->set('modx_path',$modx_path);

        $output->writeln('Ready');
    }


    /**
     * Check if path contains a MODx install
     *
     * @param string $path Path to check for MODx install
     * @return bool
     */
    protected function containsModxInstance($path)
    {
        define('MODX_API_MODE', true);
        require $path . DIRECTORY_SEPARATOR . 'index.php';

        if (!isset($modx) || !$modx instanceof \modX) {
            unset($modx);
            return false;
        }
        unset($modx);
        return true;
    }


    /**
     * Write config file to disk in the current directory
     *
     * @param string $path Path to modx installation
     */
    protected function dropConfigFile($path)
    {
        $data = new \Xtrz\Modx\ModxWrapper($path);
        $here = getcwd();
        file_put_contents($here.DIRECTORY_SEPARATOR.'/modx-cli.json',$data);
    }


}