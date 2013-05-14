<?php

namespace AlanPich\Modx\CLI;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \phpDocumentor\Reflection\DocBlock;


/**
 * Class AbstractCommand
 *
 * @package AlanPich\Modx\CLI
 */
abstract class AnnotatedCommand extends \Symfony\Component\Console\Command\Command
{
    protected $interactive = false;

    protected $globals = array();
    protected $_globals;

    protected function configure()
    {
        parent::configure();


        // Populate description from docBlock
        $class = get_called_class();
        $reflection = new \ReflectionClass($class);
        $docBlock = new DocBlock($reflection);

        $shortDescription = $docBlock->getShortDescription();
        $commandName = $docBlock->getTagsByName('command')[0]->getContent();
        $longDescription = $docBlock->getLongDescription()->getContents();

        $this->setDescription($shortDescription);
        $this->setHelp($longDescription);
        $this->setName($commandName);

        $this->defineCommandOptions();

    }


    /**
     * Define command options and arguments
     */
    protected function defineCommandOptions(){}



    protected function globals($key){
        if(is_null($this->_globals)){
            $this->_globals = $this->getApplication()->getConfig();
        }
        return $this->_globals->$key;
    }


}
