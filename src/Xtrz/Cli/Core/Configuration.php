<?php

namespace Xtrz\Cli\Core;

/**
 * Class Configuration
 *
 */
class Configuration
{

    /**
     * Path to MODx installation
     * @var string
     */
    protected $_config_file;
    protected $_global_file;
    protected $_globals;
    protected $_locals;
    protected  static $_instance;

    /**
     * Create new configuration object.
     * Loading order is class defaults, then global config, then supplied config file (if found)
     * @param $file
     */
    public function __construct($file = '')
    {
        $this->_global_file = MODX_CLI_TOOL . "config/global.config.php";
        $this->_config_file = $file;

        $this->_loadGlobalConfig();
        $this->_loadSuppliedConfig($file);
    }


    /**
     * Maintain a single instance of self
     *
     * @return Configuration
     */
    public static function getInstance(){
        if(is_null(self::$_instance)){
            self::$_instance = new Configuration( getcwd().DIRECTORY_SEPARATOR.'modx-cli.json');
        }
        return self::$_instance;
    }


    /**
     * Load global config file
     */
    protected function _loadGlobalConfig()
    {
        if (!is_readable($this->_global_file)){
            return;
        }
        $this->_globals = include $this->_global_file;
    }

    /**
     * Load config from a file specified by a file path
     *
     * @param string $path
     */
    protected function _loadSuppliedConfig($path)
    {
        if (!is_readable($path))
            return;

        $json = file_get_contents($path);
        $data = json_decode($json);
        if (is_null($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            $this->_locals[$key] = $value;
        }
    }

    /**
     * Get a property #
     *
     * @param string $key
     * @return null
     */
    public function __get($key)
    {
        if (isset($this->_locals[$key]))
            return $this->_locals[$key];

        // Check globals
        if (isset($this->_globals[$key]))
            return $this->_globals[$key];

        // NOT FOUND
        return null;
    }

    /**
     * Set the value of a local property
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->_locals[$key] = $value;
        $this->_persistLocals();
    }

    /**
     * Save local config to disk
     */
    protected function _persistLocals()
    {
        $json = json_encode($this->_locals, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($this->_config_file, $json . "\n" );
    }

    /**
     * Set the value of a global property
     * @param string $key
     * @param mixed $value
     */
    public function setGlobal($key, $value)
    {
        $this->_globals[$key] = $value;
        $this->_persistGlobals();
    }

    /**
     * Save global config to disk
     */
    protected function _persistGlobals()
    {
 //       $json = json_encode($this->_globals, JSON_PRETTY_PRINT);
   //     file_put_contents($this->_globals, $json);
    }

    public function getModx()
    {
        $path = stripslashes($this->modx_path);
        if(strlen($path)){
            if(!defined('MODX_API_MODE')){
                define('MODX_API_MODE',true);
            }
            include $path.DIRECTORY_SEPARATOR.'index.php';
            $modx->initialize('mgr');
            $modx->getService('error','error.modError');
            return $modx;
        }
    }


    public function toArray(){
        return array_merge($this->_globals,$this->_locals);
    }

}