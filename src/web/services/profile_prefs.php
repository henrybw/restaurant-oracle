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

  //$data = array();

  $query_statement = 'select * from user_pref_categories where uid="' . sanitize($user) . '"';
  $query = db()->prepare($query_statement);
  $query->execute();

  $data = $query->fetchAll();

  return $data;
}

?>