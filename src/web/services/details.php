<?php
/**
 * Web service to query the database for restaurant details.
 * 
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');
require_once 'includes/common.php';

// If file is requested directly, return the service's data encoded in JSON
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	header('Content-Type: text/plain');
	
	// Verify that the request was well-formed
	if (!verify_args($_GET, array('id')))
		die_with_status('Invalid request', 400);
	
	$id = (int)$_GET['id'];
	$data = service_get_data($id);
	
	// Empty results are returned as a null array
	if (!$data)
		die_with_status('Restaurant not found', 404);
	
	echo json_encode($data);
}

//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

/**
 * TODO: document this
 *
 * @param integer $id The ID of the restaurant to view.
 * @return array An associative array of data for the view to display.
 *               Returns null if the restaurant does not exist.
 */
function service_get_data($id)
{
	$query = db()->prepare('select r.*, md.* from restaurants r ' .
	                          'join restaurant_metadata md on md.rid = r.rid ' .
	                       'where r.rid = ?');
	$query->execute(array($id));
		
	return ($query->rowCount() > 0) ? $query->fetch() : NULL;
}

?>
