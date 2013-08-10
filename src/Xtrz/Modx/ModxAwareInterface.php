<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alan
 * Date: 10/08/13
 * Time: 14:40
 * To change this template use File | Settings | File Templates.
 */

namespace Xtrz\Modx;


interface ModxAwareInterface {

    /**
     * Return $modx runtime instance
     *
     * @return \modX
     */
    public function getModx();


    /**
     * Set modx instance
     *
     * @param \modX $modx
     * @return mixed
     */
    public function setModx(\modX $modx);

}
