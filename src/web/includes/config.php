<?php
/**
 * Database configuration file.
 * 
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

// File should never be requested directly
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	include('404.shtml');
	die();
}

$db_name = 'htw_restaurant_oracle';
$db_user = 'htw';
$db_pass = '7RRcs7dp';

