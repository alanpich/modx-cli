<?php

namespace Xtrz\Modx;

use Xtrz\Modx\Installer\Exception;
use Xtrz\Util\Filesystem;
use Xtrz\Util\Version;

/**
 * Represents a component package in development
 *
 * @package Xtrz\Modx
 */
class Pkg
{
    /** @var  string */
    protected $name;
    /** @var  string */
    protected $namespace;
    /** @var  string */
    protected $author;
    /** @var  Version */
    protected $version;
    /** @var  string Path to save config at */
    protected $filePath;
    /** @var ServiceManager */
    protected $serviceManager;
    /** @var  string */
    protected $modx_path;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->version = new Version;
    }

    /**
     * Create an instance by parsing a json file
     *
     * @param string $path Path to json file
     * @return static
     * @throws Exception\PkgException
     */
    public static function fromFile($path)
    {
        if (!is_readable($path)) {
            throw new Exception\PkgException("Pkg file $path does not exist");
        }
        $data = Filesystem::parseJSONFile($path);

        $instance = new static();

        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            $instance->$method($value);
        }

        $instance->setFilePath($path);

        return $instance;
    }

    /**
     * @return \Xtrz\Modx\ServiceManager
     */
    public function getServiceManager()
    {
        if (!$this->serviceManager) {
            $this->setServiceManager(new ServiceManager());
        }
        return $this->serviceManager;
    }

    /**
     * @param \Xtrz\Modx\ServiceManager $serviceManager
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getModxPath()
    {
        return $this->modx_path;
    }

    /**
     * @param string $modx_path
     */
    public function setModxPath($modx_path)
    {
        $this->modx_path = $modx_path;
    }

    /**
     * Return an array of stuff
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'namespace' => $this->getNamespace(),
            'author' => $this->getAuthor(),
            'version' => (string) $this->getVersion(),
            'modx_path' => $this->getModxPath()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return \Xtrz\Util\Version
     */
    public function getVersion()
    {
        return (string)$this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version->setFromString($version);
    }

    /**
     * Persist any changes to the filesystem
     */
    public function persist()
    {
        if(!$this->getFilePath()){
            throw new Exception("No filepath specified for Pkg persistance");
        }
        $json = json_encode($this->toArray(),JSON_PRETTY_PRINT);
        Filesystem::write($this->getFilePath(),$json."\n");
    }


}
