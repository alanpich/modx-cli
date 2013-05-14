<?php
use AlanPich\Modx\CLI\ModxCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Clears the MODx cache
 *
 * Clears the MODx cache of an installation specified by --path
 *
 * @command system-setting
 */
class SystemSettingCommand extends ModxCommand
{

    protected function defineCommandOptions()
    {

        $this->addArgument('key', InputArgument::REQUIRED);
        $this->addArgument('value', InputArgument::OPTIONAL);

    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);


        print_r($this->globals('install.cmsadmin'));


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


    protected function getSystemSetting($key, InputInterface $input, OutputInterface $output)
    {
        $value = $this->modx->getOption($key);
        $output->writeln($value);
    }

    protected function setSystemSetting(\modSystemSetting $setting, $value, InputInterface $input, OutputInterface $output)
    {

    }

}