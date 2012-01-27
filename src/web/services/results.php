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
	$preferences = service_get_preferences($group);
	
	//TODO: Proof of concept. Simply returns entire restaurants table
	$query = db()->prepare('select * from restaurants r, restaurant_metadata md where r.rid=md.rid');
	$query->execute();

	$results = $query->fetchAll();
	
	return array($preferences, $results);
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
	
	print('User ID = ' . current_user();
	
	if (!empty($group)) {
		print('Group ID = ' . $group);
		$queryId[] = $group;
		$query = db()->prepare('select up.*, u.fname, u.lname from user_pref_categories up, group_members gm, users u where gm.uid = up.uid and gm.gid=? and u.uid=gm.uid order by up.uid');
	} else {
		$queryId[] = current_user();
		$query = db()->prepare('select u.fname, u.lname, up.* from users u, user_pref_categories up where u.uid = ? and up.uid=u.uid');
	}
	
	$query->execute($queryId);
	$preferences = $query->fetchAll();
	
	return $preferences;
}

?>
