<?php
/**
 * Web service to query the currently logged-in user. Returns NULL if no one is logged in.
 * 
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');
require_once 'includes/common.php';

// If file is requested directly, return the service's data encoded in JSON
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	header('Content-Type: text/plain');
	echo json_encode(service_get_data());
}

//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

/**
 * TODO: document this
 *
 * @return array An associative array of data for the view to display, which
 *               will just have one entry: uid => 
 *               Returns null if there is n
 */
function service_get_data()
{
	return (logged_in()) ? array('uid' => current_user()) : NULL;
}

?>
