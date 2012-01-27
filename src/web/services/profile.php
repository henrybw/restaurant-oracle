<?

set_include_path(get_include_path() . PATH_SEPARATOR . '../');
require_once 'includes/common.php';

// If file is requested directly, return the service's data encoded in JSON
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	header('Content-Type: text/plain');
	echo json_encode(service_get_data());
}


function service_get_data($profile_id)
{
  // make sure profile_id exists

  if (!isset($profile_id)) {
    return null;
  }


  $data = array();

  $profile_id_sanitized = sanitize($profile_id);
  $query_statement = "select * from users where uid='$profile_id_sanitized'";

  $query = db()->prepare($query_statement);
  $query->execute();


  $data = $query->fetch(PDO::FETCH_ASSOC);

  return $data;
}

?>