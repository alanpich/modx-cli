<?php
use AlanPich\Modx\CLI\AnnotatedCommand;
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
class InitCommand extends AnnotatedCommand {


    /**
     *
     */
    protected function configure()
    {
        parent::configure();

    }

        /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        $key = $input->getArgument('key');
        $value = $input->getArgument('value');

        $setting = $this->modx->getObject('modSystemSetting',array(
            'key' => $key
        ));

        if(!$setting instanceof \modSystemSetting){
            $output->writeln("<error>System Setting {$key} not found</error>");
            return 1;
        }

        if (is_null($value)) {
            $this->getSystemSetting($key, $input, $output);
        } else {
            $this->setSystemSetting($key, $value, $input, $output);
        }
    }


}
