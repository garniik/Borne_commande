<?php
require_once(dirname(__FILE__) . '/lib/myproject.lib.php');
$db=require(dirname((__FILE__)) . '/lib/mypdo.php' );
if (GETPOST('debug') == true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

include 'main.inc.php';
