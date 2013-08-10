<?php
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xtrz\Cli\Core\Command as Command;


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

        $output->writeln('<info>Creating MODX Component package</info>');

        // Create a brand new package
        $pkg = new \Xtrz\Modx\Pkg;
        $pkg->setFilePath( getcwd().'/xtrz.json');

        // Set package name
        $pkg->setName($dialog->ask($output,'<comment>Package name:</comment> '));

        // Set namespace
        $defaultNamespace = strtolower($pkg->getName());
        $pkg->setNamespace($dialog->ask($output, '<comment>Namespace:</comment> ['.$defaultNamespace.'] ', $defaultNamespace));

        // Set Author
        $defaultAuthor = get_current_user();
        $pkg->setAuthor($dialog->ask($output,'<comment>Author:</comment> ['.$defaultAuthor.'] ',$defaultAuthor));

        // Set version
        $defaultVersion = '0.0.0';
        $pkg->setVersion($dialog->ask($output,'<comment>Version:</comment> ['.$defaultVersion.'] ', $defaultVersion));

        $output->writeln('');

        if($dialog->askConfirmation($output,"Would you like to bind this package to a MODX installation? ",false)){
            $pkg->setModxPath( $dialog->ask($output,"Modx install path: "));
        }


        $output->writeln("");
        $output->writeln("<comment>Proposed configuration: </comment>");
        $output->write(json_encode($pkg->toArray(),JSON_PRETTY_PRINT)."\n");
        if(!$dialog->askConfirmation($output,"<comment>Create configuration file with this data?</comment>",true)){
            return;
        }


        $output->writeln('<comment>Package config file created</comment>');
        // Save pkg to disk
        $pkg->persist();
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
        file_put_contents($here.DIRECTORY_SEPARATOR.'/xtrz.json',$data);
    }


}
