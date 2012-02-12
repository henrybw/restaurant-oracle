<?
session_start();

/*
 * @author Coral Peterson
 */

require_once 'includes/common.php';

set_include_path(get_include_path() . PATH_SEPARATOR . '../');


// If file is requested directly, return the service's data encoded in JSON
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	header('Content-Type: text/plain');
	echo json_encode(service_get_data());
}


function service_get_data()
{
	$user = current_user();
	if (!isset($user)) {
		return null; // maybe die
	}
	
	$data = array();

	//$query_statement = 'select * from user_pref_categories where uid="' . sanitize($user) . '"';
	//$query = db()->prepare($query_statement);
	//$query->execute();
	
	//rewrite
	
	// Get all of the categories
	$query = db()->prepare("select * from categories cat order by name");
	$query->execute();
	$data['categories'] = $query->fetchAll();
	
	// Get the user's preferences
	$query = db()->prepare("select upc.rating as 'rating', cat.name as 'name' " .
		"from user_pref_categories upc, categories cat " .
		"where upc.uid = :uid and cat.cat_id = upc.category " .
		"order by cat.name");
	$query->bindParam(':uid', sanitize($user));
	$query->execute();
	$data['user_prefs'] = $query->fetchAll();
	
	//db()->commit();
	
	//$data['name'] = "name";
	//$data['rating'] = 'rating';
	
	return $data;
}

?>