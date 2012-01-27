<?php
/**
 * Web service to log out any currently logged in user.
 * 
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');
require_once 'includes/common.php';

if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	header('Content-Type: text/plain');
	service_get_data();  // Logout doesn't return anything
}

//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

/**
 * TODO: document this
 *
 * @return NULL
 */
function service_get_data()
{
	// This just clears session data, so this will be fine to call even
	// if there is no user currently logged in.
	logout();
	return NULL;
}

?>
