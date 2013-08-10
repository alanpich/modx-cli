<?php
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xtrz\Cli\Core\Command as Command;
use Xtrz\Modx\Skeleton\SkeletonBuilder as Skeleton;

/**
 * Package component into a transport package zip
 *
 * @command build
 */
class BuildCommand extends Command
{

    /**
     * Configure Arguments & Options
     */
    public function configure()
    {
        parent::configure();

        $conf = getcwd().'/_build/build.config.php';
        if(is_readable($conf)){
            include $conf;
        }

        $this->addArgument("type", InputArgument::OPTIONAL, "Create an element skeleton");
        //    $this->addOption("force","f", InputOption::VALUE_NONE, "Force overwrite of existing config file");

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws Exception
     * @return int|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');


        $output->writeln('<info>Building transport package</info>');

        // Scan for snippets
        $elementManager = new Xtrz\Modx\Element\ElementManager();
        $elementManager->setPkg($this->getPkg());
        $elementManager->scanFilesystem(PKG_CORE);


        // Get the package builder service
        /** @var \Xtrz\Modx\Builder\TransportPackageBuilder $builder */
        $builder = $this->getPkg()->getService('Xtrz\Modx\Builder\TransportPackageBuilder');

        // Get array of all elements and add to package
        $elements = $elementManager->getxPDOObjects();

        $builder->addElements($elements);


        $builder->buildTransportPackage();


        $elements = $elementManager->getElements();

        $output->writeln('  Snippets: '.count($elements['snippets']));


        return 0;

    }

}

;
