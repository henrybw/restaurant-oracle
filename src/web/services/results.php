<?php
/**
 * Search web service
 * 
 * @author Laure Thompson <laurejt@cs.washington.edu>
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
 * @return array An associative array of data for the view to display.
 */
function service_get_results($group)
{
	// Return the preferences for the group or user
	service_get_preferences($group);
	
	//TODO: Proof of concept. Simply returns entire restaurants table
	$query = db()->prepare('select * from restaurants r, restaurant_metadata md where r.rid=md.rid');
	$query->execute();

	return $query->fetchAll();
}

/**
 * TODO: document this
 *
 * @return array An associative array of data for the view to display.
 */
function service_get_preferences($group)
{
	$preferences = array();
	
	$queryId = array();
	
	if (isset($group)) {
		//$queryId[] = $group;
		$query = db()->prepare('select * from groups where gid = ?');
	
		/* TODO: Proof of concept. Add querying for actual group
		 * prefrences later...
		 */
	
	} else {
		//$queryId[] = $user;
		$query = db()->prepare('select * from users where uid = ?');
	
		/* TODO: Proof of concept. Add querying for actual user
		 * prefrences later...
		 */
	
	}
	
	//TODO: Remove hack.
	$queryId[] = 1;
	
	$query->execute(queryId);

	$data = $query->fetchAll();
	return $preferences;
	
}

?>
