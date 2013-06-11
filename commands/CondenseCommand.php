<?php
use AlanPich\Modx\CLI\ModxCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Imports a vapour package into a MODx installation
 *
 * Imports a vapour package into a MODx installation
 *
 * @command condense
 */
class CondenseCommand extends ModxCommand
{

    protected function defineCommandOptions()
    {

        $this->addArgument("pkg", InputArgument::REQUIRED, "Path to Vapour zip file");

    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);


        // Check pkg exists
        $pkg = $input->getArgument('pkg');
        if(!is_readable($pkg)){
            $output->writeln("<error>Invalid pkg [{$pkg}]</error>");
            return 1;
        }

        $fileName = basename($pkg);
        $signature = str_replace('.transport','',pathinfo($pkg,PATHINFO_FILENAME));
         $siteName = $this->modx->getOption('site_name');
        $corePath = $this->modx->getOption('core_path');

        $output->writeln("<info>Importing Vapour package into {$siteName}</info>");

        $this->modx->setLogTarget("ECHO");

        // Copy Vapour package to core/packages
        copy($pkg,$corePath."packages/".$fileName);

        echo ">>>>> {$signature}\n";

        // Create modTransportPackage object in new modx install
        $scan = $this->modx->runProcessor('workspace/packages/scanlocal');

        print_r($scan);

        echo "\n";

        // Run install command
        $response = $this->modx->runProcessor('workspace/packages/install',array(
                'signature' => $signature
            ));

        print_r($response);

    }


}
