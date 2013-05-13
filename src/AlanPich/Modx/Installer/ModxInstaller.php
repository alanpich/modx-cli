<?php
namespace AlanPich\Modx\Installer;


class ModxInstaller
{

    /** Shows the latest version number */
    const INFO_PAGE = 'http://modx.com/download/';

    /** Url for MODx downloaders */
    const DOWNLOAD_PAGE = 'http://modx.com/download/direct/';

    /** Minimum required PHP version */
    const PHP_REQUIRED_VERSION = '5.3.0';

    /** Relative path of directory to store installer zips */
    const ZIP_DIR = 'dist/';

    /** @var string Path to directory storing modx installer zips */
    public $zipDir;

    public function __construct()
    {
        // Check environment is acceptable
        $this->checkEnvironment();

        $this->zipDir = MODX_CLI_TOOL."installers/";

        // Update the installer cache
        $this->updateInstaller();
    }


    /**
     * Check the local environment is up to scratch
     *
     * @throws \Exception
     */
    public function checkEnvironment()
    {
        error_reporting(E_ALL);
        // Test PHP version.
        if (version_compare(phpversion(), self::PHP_REQUIRED_VERSION, '<')) {
            abort(
                sprintf("Sorry, this script requires PHP version %s or greater to run.", self::PHP_REQUIRED_VERSIONcc)
            );
        }
        if (!extension_loaded('curl')) {
            abort("Sorry, this script requires the curl extension for PHP.");
        }
        if (!class_exists('ZipArchive')) {
            abort("Sorry, this script requires the ZipArchive classes for PHP.");
        }
        // timezone
        if (!ini_get('date.timezone')) {
            abort(
                "You must set the date.timezone setting in your php.ini. Please set it to a proper timezone before proceeding."
            );
        }
    }


    /**
     * Update the local installer cache to the newest version
     */
    public function updateInstaller()
    {
        // Grab the latest modx version
        $version = $this->get_latest_modx_version();
        // Do we have an installer for this version locally?
        if (!$zipFile = $this->_getLocalZipFile($version)) {
            $this->_downloadInstallerZip($version);
        }
    }


    /**
     * Create a new MODx installation at target
     *
     * @param ModxInstallerConfig $config
     * @param string              $version
     *
     * @throws Exception
     */
    public function install(ModxInstallerConfig $config, $version = '')
    {
        // Sanitize target path
        $target = rtrim($config->context_web_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Grab the version number
        if (!strlen($version)) {
            $version = $this->get_latest_modx_version();
        }

        // Check the installer file exists
        if (!$zip = $this->_getLocalZipFile($version)) {
            $this->updateInstaller();
        }
        if (!$zip = $this->_getLocalZipFile($version)) {
            throw new Exception("Unable to find local installer for version $version");
        }

        // Extract the zip archive to target
        $this->_extractZipFile($zip, $target);

        // Prepare globals file zip
        $xmlFile = $target . 'setup/config.xml';
        $config->toXML($xmlFile);

        // Prepare any directory permissions
        $this->_prepare_modx($config);

        // Make sure the database exists - modx is shit at creating DBs
        $this->createDatabase($config);

        // Do the install
        unset($argv);
        $argv[1] = '--installmode=new';
        ob_start();
        include($target . 'setup/index.php');
        ob_end_clean();


    }


    /**
     * Manually create the database if it doesnt already exist.
     * ModX installer fails 9 times out of 10 when trying to create it
     *
     * @param ModxInstallerConfig $config
     * @throws Exception
     */
    public function createDatabase(ModxInstallerConfig $config)
    {
        if (!$db = new \mysqli($config->database_server, $config->database_user, $config->database_password)) {
            throw new Exception("Invalid database connection details");
        }

        if (!$db->select_db($config->database)) {
            if (!$db->query("CREATE DATABASE {$config->database}")) {
                throw new Exception("Failed to created database {$config->database}");
            }
        }

    }


    /**
     * ZipArchive::extractTo did not do what I wanted, and it had errors. Boo.
     * The trick is to shift the "modx-2.2.6-pl" off from the front of the
     * extraction. Instead of extracting to public_html/modx-2.2.6-pl/ we want
     * to extract straight to public_html/
     * I couldn't find any other examples that did quite what I wanted.
     *
     * See http://stackoverflow.com/questions/5256551/unzip-the-file-using-php-collapses-the-zip-file-into-one-folder
     *
     * @param        $zip
     * @param string $target path where we want to setup MODX, e.g. public_html/
     *
     */
    protected function _extractZipFile($zip, $target)
    {
        $z = zip_open($zip);

        while ($entry = zip_read($z)) {

            $entry_name = zip_entry_name($entry);

            // only proceed if the file is not 0 bytes long
            if (zip_entry_filesize($entry)) {
                // Put this in our own directory
                $entry_name = $target . self::strip_first_dir($entry_name);
                $dir = dirname($entry_name);
                // make all necessary directories in the file's path
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
                $file = basename($entry_name);
                if (zip_entry_open($z, $entry)) {
                    if ($fh = fopen($dir . '/' . $file, 'w')) {
                        // write the entire file
                        fwrite(
                            $fh,
                            zip_entry_read($entry, zip_entry_filesize($entry))
                        )
                            or error_log("can't write: $php_errormsg");
                        fclose($fh) or error_log("can't close: $php_errormsg");
                    } else {
                        print "Can't open $dir/$file" . PHP_EOL;
                    }
                    zip_entry_close($entry);
                } else {
                    print "Can't open entry $entry_name" . PHP_EOL;
                }
            }
        }
    }


    /**
     * Set up a few things in MODX...
     *
     * @param ModxInstallerConfig $config
     */
    protected function _prepare_modx(ModxInstallerConfig $config)
    {
        $base_path = $config->context_web_path;
        $core_path = $config->core_path;
        // Check that core/cache/ exists and is writeable
        if (!file_exists($core_path . 'cache')) {
            @mkdir($core_path . 'cache', 0777, true);
        }
        if (!is_writable($core_path . 'cache')) {
            chmod($core_path . 'cache', DIR_PERMS);
        }

        // Check that core/components/ exists and is writeable
        if (!file_exists($core_path . 'components')) {
            @mkdir($core_path . 'components', 0777, true);
        }
        if (!is_writable($core_path . 'components')) {
            chmod($core_path . 'components', DIR_PERMS);
        }

        // Check that assets/components/ exists and is writeable
        if (!file_exists($base_path . 'assets/components')) {
            @mkdir($base_path . 'assets/components', 0777, true);
        }
        if (!is_writable($base_path . 'assets/components')) {
            chmod($base_path . 'assets/components', DIR_PERMS);
        }

        // Check that core/export/ exists and is writable
        if (!file_exists($core_path . 'export')) {
            @mkdir($core_path . 'export', 0777, true);
        }
        if (!is_writable($core_path . 'export')) {
            chmod($core_path . 'export', DIR_PERMS);
        }

        // touch the globals file
        if (!file_exists($core_path . 'config/globals.inc.php')) {
            @mkdir($core_path . 'config', 0777, true);
            touch($core_path . 'config/config.inc.php');
        }
        if (!is_writable($core_path . 'config/config.inc.php')) {
            chmod($core_path . 'config/config.inc.php', DIR_PERMS);
        }
    }


    /**
     * Check for a locally cached installer zip for a specific version
     *
     * @param string $version
     *
     * @return bool|string
     */
    protected function _getLocalZipFile($version)
    {
        $file = $this->zipDir . "modx-{$version}.zip";
        return is_readable($file) ? $file : false;
    }


    /**
     * Download the installer zip for a specific version
     */
    protected function _downloadInstallerZip($version, $progressCallback = null)
    {
        $zip_url = self::DOWNLOAD_PAGE . "modx-{$version}.zip";

        $localPath = $this->zipDir . "modx-{$version}.zip";


        $fp = fopen($localPath, 'w+');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $zip_url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        if (is_callable($progressCallback)) {
            curl_setopt($ch, CURLOPT_NOPROGRESS, false); // req'd to allow callback
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $progressCallback);
        } else {
            curl_setopt($ch, CURLOPT_NOPROGRESS, true); // req'd to allow callback
        }
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 128); // bigger = fewer callbacks
        if (curl_exec($ch) === false) {
            throw new Exception("Failed to download zip file");
        }
        curl_close($ch);
        fclose($fp);
    }


    /**
     * Finds the name of the lastest stable version of MODX
     * by scraping the MODX website.  Prints some messaging...
     *
     * @return string
     */
    function get_latest_modx_version()
    {
        $contents = file_get_contents(self::INFO_PAGE);
        preg_match(
            '#' . preg_quote('<h3>MODX Revolution ') . '(.*)' . preg_quote('</h3>', '/') . '#msU',
            $contents,
            $m1
        );
        if (!isset($m1[1])) {
            abort('Version could not be detected on ' . self::INFO_PAGE);
        }
        return $m1[1];
    }


    /**
     * Strip the front off the dir name to make for cleaner zipfile extraction.
     * Converts something like myzipdir/path/to/file.txt
     * to path/to/file.txt
     *
     * Yes, this is some childish tricks here using string reversal, but we
     * get the biggest bang for our buck using dirname().
     *
     * @param string $path
     *
     * @return string
     */
    public static function strip_first_dir($path)
    {
        $path = strrev($path);
        $path = dirname($path);
        $path = strrev($path);
        return $path;
    }

}
