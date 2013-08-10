<?php
$G = array();


/**
 * Absolute Paths to directories to be scanned for commands
 * - Add third-party extensions through here
 *
 * @var array of string
 */
$G['includes'] = array(
        dirname(__DIR__)."/src/Xtrz/Cli/Command",
  //      dirname(__DIR__)."/commands",
    );

$G['templatePath'] = dirname(__DIR__).'/templates/';



return $G;
/**
{
    "include": [

        ],
    "install.database_user": "root",
    "install.database_password": "password",
    "install.cmsadmin": "alan",
    "install.cmspassword": "password",
    "install.cmsadminemail": "alan.pich@gmail.com",
    "install.checkversiononinstall": "0"
}
*/



