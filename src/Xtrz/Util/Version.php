<?php

namespace Xtrz\Util;

/**
 * Xtrz\Util\Version
 */
class Version
{
    protected $major = 0;
    protected $minor = 0;
    protected $patch = 0;


    public function setFromString($version)
    {
        try{
            $bits = explode('.', $version);
            $this->major = (int)array_shift($bits);
            $this->minor = (int)array_shift($bits);
            $this->patch = (int)array_shift($bits);
        } catch (\Exception $E){
            return false;
        }
    }

    /**
     * Increase patch version by 1
     */
    public function incrementPatch()
    {
        $this->patch += 1;
    }

    public function incrementMinor(){
        $this->minor += 1;
        $this->patch = 0;
    }

    public function incrementMajor(){
        $this->major += 1;
        $this->minor = 0;
        $this->patch = 0;
    }

    public function __toString(){
        return implode('.',array(
                $this->major,
                $this->minor,
                $this->patch
            ));
    }

    /**
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @param int $major
     */
    public function setMajor($major)
    {
        $this->major = $major;
    }

    /**
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @param int $minor
     */
    public function setMinor($minor)
    {
        $this->minor = $minor;
    }

    /**
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @param int $patch
     */
    public function setPatch($patch)
    {
        $this->patch = $patch;
    }

}
