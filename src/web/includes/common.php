<?php
/**
 * Common functionality for all pages.
 * 
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

session_start();
set_include_path(get_include_path() . PATH_SEPARATOR . '../');

// File should never be requested directly
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	include('404.shtml');
	die();
}

define('DEBUG', true);  // TODO: REMOVE THIS IN PRODUCTION!!!
define('DATE_FORMAT', 'Y-m-d H:i:s');

require_once 'functions.php';

define('VIEW_PATH', 'views/');
require_once VIEW_PATH . '_common.php';

?>
