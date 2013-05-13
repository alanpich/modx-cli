<?php

namespace AlanPich\Modx\Installer;


class ModxInstallerConfig
{

    /** @var string Database type (usually mysql) */
    public $database_type = 'mysql';

    /** @var string Database server */
    public $database_server = 'localhost';

    /** @var string Database name */
    public $database = NULL;

    /** @var string Database user */
    public $database_user = NULL;

    /** @var string Database password */
    public $database_password = NULL;

    /** @var string Database connection charset */
    public $database_connection_charset = 'utf8';

    /** @var string Database charset */
    public $database_charset = 'utf8';

    /** @var string Database collation */
    public $database_collation = 'utf8_general_cli';

    /** @var string Table prefix */
    public $table_prefix = 'modx_';

    /** @var int HTTPS port */
    public $https_port = 443;

    /** @var string HTTP server hostname */
    public $http_host = 'localhost';

    /** @var int Disable MODx cache (0|1) */
    public $cache_disabled = 0;

    /** @var int Was MODx source from git (0|1) */
    public $inplace = 0;

    /** @var int Has the core package already been extracted (0|1) */
    public $unpacked = 0;

    /** @var string Default language */
    public $language = 'en';

    /** @var string Superuser username */
    public $cmsadmin = NULL;

    /** @var string Superuser password */
    public $cmspassword = NULL;

    /** @var string Superuser email address */
    public $cmsadminemail = NULL;

    /** @var string Core directory path */
    public $core_path = NULL;

    /** @var string Manager directory path */
    public $context_mgr_path = NULL;

    /** @var string Manager url */
    public $context_mgr_url = NULL;

    /** @var string Connectors dir path */
    public $context_connectors_path = NULL;

    /** @var string Connectors dir URL */
    public $context_connectors_url = NULL;

    /** @var string Site root path */
    public $context_web_path = NULL;

    /** @var string Site URL */
    public $context_web_url = NULL;

    /** @var int Remove setup dir after install (0|1) */
    public $remove_setup_directory = 1;


    public function __construct($data = array())
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }



    /**
     * Output globals as XML document, optionally
     * writing it to a file
     *
     * @param string $filePath
     * @throws ConfigOptionNotSetException
     */
    public function toXML($filePath = '')
    {
        $xml = new \DOMDocument();
        $root = $xml->createElement("modx");

        foreach ($this as $key => $value) {
            if (is_null($value)) {
                throw new ConfigOptionNotSetException("Config key [$key] needs to be set");
            }
            if(is_bool($this->$key))
                $value = $value? 1: 0;
            $root->appendChild($xml->createElement($key, $value));
        }
        $xml->appendChild($root);

        if (strlen($filePath)) {
            $xml->save($filePath);
        }

    }


    /**
     * CLI helper tool to populate all data fields via CLI input
     *
     * @param bool     $minimal    If true, only prompt for fields that are currently NULL
     * @param callable $promptFunc Optional function to call to retrieve user input
     */
    public function interactivePopulation($minimal = false, $promptFunc = NULL)
    {
        foreach ($this as $key => $value) {
            if ($minimal && !is_null($value)) {
                continue;
            }

            if (is_callable($promptFunc)) {
                $newVal = call_user_func_array($promptFunc,array($key, $value));
            } else {
                $default = is_null($value) ? '' : " [{$value}]";
                $newVal = readline("$key{$default}: ");
            }

            if (strlen($newVal) > 0) {
                $this->$key = $newVal;
            }

        }
    }


}


class ConfigOptionNotSetException extends Exception
{
}
