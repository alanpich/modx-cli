<?php

namespace Xtrz\Modx\Skeleton;

use Xtrz\Modx\Pkg;
use Xtrz\Util\Filesystem;

/**
 * Xtrz\Modx\Skeleton\Skeleton
 */
class SkeletonBuilder
{

    protected $config;

    /** @var \Xtrz\Modx\Pkg  */
    protected $pkg;


    public function __construct( Pkg $pkg)
    {
        $this->pkg = $pkg;
    }

    public function buildFromTemplate($tplName,$outputPath,$params = array())
    {
        $data = array();
        foreach($this->pkg->data as $key => $val){
            $data[strtoupper('pkg_'.$key)] = $val;
        }
        $data = array_merge($data,$params);

        $sourceDir = $this->config['template_path'].$tplName.'/';
        Filesystem::recursiveCopy($sourceDir,$outputPath,$data);

    }


}
