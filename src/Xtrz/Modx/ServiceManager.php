<?php

namespace Xtrz\Modx;

/**
 * Xtrz\Modx\ServiceManager
 */
class ServiceManager
{

    protected $aliases;


    public function get( $class )
    {
        try {
            $obj = new $class;
        } catch(\Exception $E){
            throw new \Exception("ServiceManager unable to create $class");
        }

        if($obj instanceof PkgAwareInterface)
            $obj->setPkg($pkg);

    }


}
