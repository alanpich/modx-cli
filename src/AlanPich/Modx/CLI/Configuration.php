<?php

namespace AlanPich\Modx\CLI;

class Configuration
{

    protected $_configFile;

    public $data = array();


    public function __construct()
    {
        $this->_configFile = MODX_CLI_TOOL . "config/global.json";

        // Load globals
        if (is_readable($this->_configFile)) {
            $json = file_get_contents($this->_configFile);
            $data = json_decode($json, true);
            if (!is_null($data)) {
                $this->data = $data;
            } else {
                echo "ERROR: ".json_last_error()."\n";
            }
        } else {
            echo "ERROR: cant load globals from {$this->_configFile}\n";
        }
    }


    public function __get($key)
    {
        return @ $this->data[$key];
    }

    public function __set($key, $value)
    {
        if (substr($key, 0, 1) == '_') {
            return;
        }
        $this->data[$key] = $value;
        $this->save();
    }


    public function toArray()
    {
        return $this->data;
    }


    protected function save()
    {
        $json = json_encode($this->data, JSON_PRETTY_PRINT);
        file_put_contents($this->_configFile, $json);
    }


}
