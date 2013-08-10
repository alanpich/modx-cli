<?php
define('PKG_NAME', '___PKG_NAME___');
define('PKG_NAMESPACE', '___PKG_NAMESPACE___');
define('PKG_VERSION','1.0.0');
define('PKG_RELEASE','alpha');

define('PKG_ROOT',dirname(dirname(__FILE__)).'/');
define('PKG_CORE',PKG_ROOT.'core/components/'.PKG_NAMESPACE.'/');
define('PKG_ASSETS',PKG_ROOT.'assets/components/'.PKG_NAMESPACE.'/');
define('PKG_BUILD',PKG_ROOT.'_build/');
//define('PKG_COMMIT',Tools::getGitCommitId(PKG_ROOT));
require PKG_ROOT.'config.core.php';
