<?php

namespace Xtrz\Cli\Core;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xtrz\Modx\Pkg;


/**
 * A console command that requires a Pkg to operate
 *
 * @package Xtrz\Cli\Core
 */
class PkgCommand extends Command
{

    protected $pkg;


    public function execute(InputInterface $input, OutputInterface $output) {
        $this->setPkg($this->getApplication()->getPkg());
        if(!$this->pkg)
            throw new \Exception("This command can only be run in the root directory of a package");


    }


    public function getPkg()
    {
        return $this->pkg;
    }

    public function setPkg($pkg){
        $this->pkg = $pkg;
    }

}
