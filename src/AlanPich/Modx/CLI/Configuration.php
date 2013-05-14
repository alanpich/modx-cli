<?php

namespace AlanPich\Modx\CLI;

// @TODO Improve configuration to allow writing back to multiple file sources

class Configuration
{

    protected $_configFile;

    public $data = array();


    public function loadFromFile($path){
        if(!is_readable($path))
            throw new Exception("Config file not found at $path");

        $data = json_decode(file_get_contents($path));
        if(!is_null($data)){
            foreach($data as $key => $val){
                $this->data[$key] = $val;
            }
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
