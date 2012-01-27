<?
session_start();

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
    return null;
  }

  $data = array();

  $profile_id_sanitized = sanitize($user);
  $query_statement = "select g.* from groups g, group_members gm where gm.uid=$profile_id_sanitized and gm.gid = g.gid";

  $query = db()->prepare($query_statement);
  $query->execute();


  $data = $query->fetchAll(); //(PDO::FETCH_ASSOC);

  return $data;
}