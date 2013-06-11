<?php
use AlanPich\Modx\CLI\AnnotatedCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Creates a Vapor Snapshot of a MODx installation
 *
 * Creates a snapshot for easy transportation and cloning of
 * sites. Can also be used to import into the SiphonCloud
 *
 * @command vaporize
 */
class VaporCommand extends AnnotatedCommand {


    protected function defineCommandOptions()
    {
        $this->addArgument('output', InputArgument::OPTIONAL);
        $this->addOption('path','p', InputOption::VALUE_REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output){

     //   parent::execute($input, $output);

        if( is_null($input->getOption('path')) ){
            $output->writeln('<error>No MODx instance</error>');
            return 1;
        } else {
            $path = rtrim($input->getOption('path'),'/').DIRECTORY_SEPARATOR;
        }


        // Calculate output file
        $opDir = $input->getArgument('output');
        if(!is_null($opDir)){
            if(is_dir($opDir)){
               $outputDir = $opDir;
            } else {
                $outputDir = rtrim(dirname($opDir),'/').DIRECTORY_SEPARATOR;
            }
        } else {
            $outputDir = getcwd().DIRECTORY_SEPARATOR;
        }

        if(!is_dir($outputDir)){
            $output->writeln('<error>Invalid output directory</error>');
            return 1;
        }


        $output->writeln("<info>Vapor-izing MODx installation");

        define('VAPOR_MODX_DIR',$path);

        include MODX_CLI_TOOL."vendor/vapor/vapor.php";

        $zipFile = PKG_NAME .'-'.PKG_VERSION .'-'. PKG_RELEASE . ".transport.zip";

        $zipPath = $path.'core/packages/'.$zipFile;
        $outputPath = $outputDir.$zipFile;


        $output->writeln("copy {$zipPath} to {$outputPath}");
        // Copy zip to output dir
        copy($zipPath,$outputPath);

        $output->writeln("<info>MODx Site vaporized</info>");
        $output->writeln("<info>  {$outputPath}</info>");


    }
}
