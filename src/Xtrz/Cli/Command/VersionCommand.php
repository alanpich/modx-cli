<?php
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xtrz\Cli\Core\PkgCommand;
use Xtrz\Modx\Skeleton\SkeletonBuilder as Skeleton;

/**
 * Get or set package version
 *
 * @command version
 */
class VersionCommand extends PkgCommand
{

    /**
     * Configure Arguments & Options
     */
    public function configure()
    {
        parent::configure();

        $this->addArgument("newVersion", InputArgument::OPTIONAL, "Set version");
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
        parent::execute($input,$output);

        $dialog = $this->getHelperSet()->get('dialog');

        $type = $input->getArgument('newVersion');

        if (empty($type)) {
            $output->writeln('Current version <info>'.$this->getPkg()->version .'</info>');
        }


        return 0;

    }


}

;
