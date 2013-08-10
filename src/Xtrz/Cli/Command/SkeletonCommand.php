<?php
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xtrz\Cli\Core\Command as Command;
use Xtrz\Modx\Skeleton\SkeletonBuilder as Skeleton;

/**
 * Set a directory config file in the current directory
 *
 * Stores common properties like modx path
 *
 * @command skel
 */
class SkeletonCommand extends Command
{

    /**
     * Configure Arguments & Options
     */
    public function configure()
    {
        parent::configure();

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

        $type = $input->getArgument('type');
        if (empty($type)) {
            return $this->listSkeletonTypes($input, $output);
        }





        switch ($type) {
            case 'project': $this->createProject($input, $output); break;
            case 'snippet': $this->createSnippet($input, $output); break;
            case 'plugin' : $this->createPlugin($input, $output); break;
        }


        return 0;

    }

    protected function listSkeletonTypes(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Modx Component Skeleton Tool');
        $output->writeln('  Available skeleton types:');


        return '';
    }

    protected function createProject(InputInterface $input, OutputInterface $output)
    {
        // Init the Modx Pkg class
        $pkg = $this->getPkg();
        $skel = new Skeleton($pkg);
        $tplPath = $this->config->templatePath . '/project/';
        $skel->buildFromTemplate($tplPath, getcwd());
    }

    protected function createSnippet(InputInterface $input, OutputInterface $output)
    {
        // Get snippet name
        $dialog = $this->getHelperSet()->get('dialog');
        $name = $dialog->ask($output,"<question>Snippet Name:</question> ");

        // Create the snippet
        $snippet = new Xtrz\Modx\Element\Snippet($this->getPkg());
        $snippet->name = $name;
        $snippet->toFile();

        // Done
        $this->msg($output,"Created Snippet $name");
    }

    protected function createPlugin(InputInterface $input, OutputInterface $output)
    {
        // Get snippet name
        $dialog = $this->getHelperSet()->get('dialog');
        $name = $dialog->ask($output,"<question>Plugin Name:</question> ");

        // Create the snippet
        $snippet = new Xtrz\Modx\Element\Plugin($this->getPkg());
        $snippet->name = $name;
        $snippet->toFile();

        // Done
        $this->msg($output,"Created Plugin $name");
    }


}

;
