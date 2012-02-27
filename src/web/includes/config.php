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

// Credentials for Cubist
/*$db_name = 'htw_restaurant_oracle';
$db_user = 'htw';
$db_pass = '7RRcs7dp';*/

// Credentials for ChickenFactory.net (John's server)
$db_name = 'chicken2_boxcat';
$db_user = 'chicken2_boxcat';
$db_pass = '#yTkUB{XE)y~';

?>