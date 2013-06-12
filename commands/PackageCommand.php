<?php
use AlanPich\Modx\CLI\ModxCommand;
use AlanPich\Modx\PackageProvider;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Tools for working with Transport Packages
 *
 * @command package
 */
class PackageCommand extends ModxCommand
{
    /** @var PackageProvider */
    protected $provider;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @return int|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $cmd = $input->getArgument('cmd');
        $arg1 = $input->getArgument('arg1');

        switch ($cmd) {
            case 'search':
                $this->_cmd_search($arg1, $dialog, $output);
                break;
            case 'install':
                $this->_cmd_install($arg1, $dialog, $output);
                break;
        }
    }


    /**
     * Configure Arguments & Options
     */
    protected function configure()
    {
        parent::configure();
        $this->addArgument("cmd", InputArgument::REQUIRED, "Command");
        $this->addArgument("arg1", InputArgument::OPTIONAL, "Command");

        if(is_null($this->modx)){
           $this->provider = false;
        } else {
            $this->provider = new AlanPich\Modx\PackageProvider($this->modx);
        }
    }


    /**
     * Search for packages in the MODx repo
     */
    protected function _cmd_search($q, $dialog, $output)
    {
        if($this->provider===false)
            throw new Exception("No MODx provider available");

        if (!strlen($q))
            throw new Exception('Invalid search term');

        $output->writeln('<info>Searching MODx repo for <comment>' . $q . '</comment></info>');
        $results = $this->provider->search($q);

        $data = array();
        foreach ($results as $name => $signature) {
            $data[] = array($name, $signature);
        }

        $table = $this->getHelperSet()->get('table');
        $table
            ->setHeaders(array('Package', 'Signature'))
            ->setRows($data);
        $table->setLayout(\Symfony\Component\Console\Helper\TableHelper::LAYOUT_DEFAULT);
        $table->render($output);
    }



    /**
     * Install a package to MODx
     *
     * @param $packageName
     * @param $dialog
     * @param $output
     * @throws Exception
     */
    protected function _cmd_install($packageName, $dialog, $output)
    {
        if($this->provider===false)
            throw new Exception("No MODx provider available");

        if(!$this->modx || is_null($this->modx))
            throw new Exception("Not bound to a MODx install");

        $success = $this->provider->processInstall($packageName);

        if(!strlen($success)){
            $results = $this->provider->search($packageName);
            if(count($results)){
                $output->writeln("Package not found. Did you mean <info>".array_keys($results)[0].'?</info>');
                return;
            }
            throw new \Exception("Invalid package name $packageName");
        }
    }

}
