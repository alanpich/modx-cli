<?php

namespace Xtrz\Modx;

use Xtrz\Util\Filesystem;

class Pkg
{

    public $data = array(
        'name' => null,
        'name_lwr' => null,
        'name_ucf' => null,
        'namespace' => null,
        'namespace_ucf' => null,
        'author' => null,
        'modx_path' => null,
        'version' => '0.0.0'
    );
    protected $modx;
    protected $savePath;

    public function __construct($params = array())
    {
        foreach ($params as $key => $value) {
            $this->data[$key] = $value;
        }

        if (isset($this->data['version']) && is_string($this->data['version'])) {
            $this->data['version'] = new \Xtrz\Util\Version($this->data['version']);
        }
    }

    public static function fromFile($path)
    {
        $data = (array)Filesystem::parseJSONFile($path);
        $instance = new self($data);
        $instance->setSavePath($path);
        return $instance;
    }

    public function setSavePath($path)
    {
        $this->savePath = $path;
    }

    public function persist()
    {
        $json = $this->toJSON();
        Filesystem::write($this->savePath, $json);
    }

    public function toJSON()
    {
        $data = (object)$this->data;
        $data->version = (string)$data->version;
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Get a prepared array of params for doing replacements
     */
    public function exportArray()
    {
        $arr = array();
        foreach ($this->data as $key => $val) {
            $arr['PKG_' . strtoupper($key)] = $val;
        }
        return $arr;
    }

    /**
     * Set package name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->data['name'] = $name;
        $this->data['name_lwr'] = strtolower($name);
        $this->data['name_ucf'] = ucfirst($name);
    }

    /**
     * Set package namespace
     *
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->data['namespace'] = strtolower($namespace);
        $this->data['namespace_ucf'] = ucfirst($namespace);
    }

    /**
     * Set package author
     *
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->data['author'] = $author;
    }

    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    public function __set($key, $value)
    {
        if (isset($this->data[$key])) {
            $this->data[$key] = $value;
        }
    }

    /**
     * Get a Service class with DI support
     */
    public function getService($serviceClass, $params = array())
    {
        if (!class_exists($serviceClass)) {
            throw new \Xtrz\Exception("Service $serviceClass not found");
        }

        $service = new $serviceClass;
        $service->setPkg($this);

        if ($service instanceof \Xtrz\Modx\ModxAwareInterface) {
            $service->setModx($this->getModx());
        }

        if (method_exists($service, 'init')) {
            $service->init();
        }


        return $service;
    }

    /**
     * Get the modx instance bound to this package
     *
     * @return \modX
     * @throws \Exception
     */
    public function getModx()
    {
        if (!$this->modx) {
            if (!isset($this->data['modx_path'])) {
                throw new \Exception("No modx path specified");
            }

            if (!defined('MODX_API_MODE')) {
                define('MODX_API_MODE', true);
            }

            ob_start();
            require $this->data['modx_path'] . '/index.php';
            ob_end_clean();
            /** @var \modX $modx */
            $modx->initialize('mgr');
            $modx->setLogLevel(\modX::LOG_LEVEL_INFO);
            $modx->setLogTarget('ECHO');

            $this->modx = $modx;
        };

        return $this->modx;
    }

    /**
     * Increment major version
     */
    public function incrementVersionMajor()
    {
        $this->data['version']->incrementMajor();
        $this->persist();
    }

    /**
     * Increment major version
     */
    public function incrementVersionMinor()
    {
        $this->data['version']->incrementMinor();
        $this->persist();
    }

    /**
     * Increment major version
     */
    public function incrementVersionPatch()
    {
        $this->data['version']->incrementPatch();
        $this->persist();
    }

}
