#!/usr/bin/php
<?php

namespace Xtrz\Cli;


define('MODX_CLI_TOOL',dirname(__FILE__).'/');
require dirname(__FILE__) . '/vendor/autoload.php';
$application = new Application("MODx Command Line tool","1.0");
$application->run();
