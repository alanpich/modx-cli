#!/usr/bin/php
<?php
define('MODX_CLI_TOOL',dirname(__FILE__).'/');
require dirname(__FILE__) . '/vendor/autoload.php';
$application = new AlanPich\Modx\CLI\Application("MODx Command Line tool","1.0");
$application->run();
