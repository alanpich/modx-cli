<?php

namespace Xtrz\Modx;

class ModxWrapper
{
    /** @var \modX */
    protected $modx;

    /** @var string */
    public $path;


    public function __construct($path, $initMgr = false)
    {
        $this->path = $path;
        if(!defined('MODX_API_MODE'))
            define('MODX_API_MODE', 1);
        /** @var \modX $modx */
        $indexFile = rtrim($path, '/') . '/index.php';
        if (!is_readable($indexFile)) {
            throw new \Exception("MODx Installation not found at $path");
        }

        include $indexFile;
        $this->modx = $modx;

        // Load Error handler service
        $this->modx->getService('error','error.modError');

        if ($initMgr) {
            $this->modx->initialize('mgr');
        }
    }


    /**
     * Wrapper for $modx->runProcessor to extract and json_decode the response
     *
     * @param string  $action
     * @param array   $scriptProperties
     * @param array   $options
     * @return mixed
     */
    public function runProcessor($action, $scriptProperties = array(), $options = array())
    {
        /** @var \modProcessorResponse $processed */
        $processed = $this->modx->runProcessor($action,$scriptProperties,$options);

       #  print_r($processed);

        $response = $processed->getResponse();
        if(!is_string($response))
            var_dump($response);

        return json_decode($response);
    }


    /**
     * Unset $modx property before serialization
     *
     * @return array
     */
    public function __sleep()
    {
        return array('path');
    }

    /**
     * Re-establish $modx property on unserialization
     */
    public function __wakeup()
    {
        define('MODX_API_MODE', 1);
        /** @var \modX $modx */
        require rtrim($this->path, '/') . '/index.php';
        $this->modx = $modx;
    }


    /**
     * Passes method calls through to modX instance
     *
     * @param $name
     * @param $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->modx, $name), $args);
    }

    /**
     * Passes parameter gets through to modX instance
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->modx->$key;
    }

    /**
     * Passes setter requests through to modx
     *
     * @param $key
     * @param $val
     * @return mixed
     */
    public function __set($key, $val)
    {
        return $this->modx->$key = $val;
    }

    /**
     * Passthru isset request to modX instance
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->modx->$key);
    }

    /**
     * Passthru unset requests to modX instance
     *
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->modx->$key);
    }
}
