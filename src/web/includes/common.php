<?php
/**
 * Common functionality for all pages.
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

// File should never be requested directly
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	include('../404.shtml');
	die();
}

define('DEBUG', true);  // TODO: REMOVE THIS IN PRODUCTION!!!

require_once 'functions.php';

define('VIEW_PATH', 'views/');
require_once VIEW_PATH . '_common.php';

?>
