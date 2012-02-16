<?
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');
require_once 'includes/common.php';


// If file is requested directly, return the service's data encoded in JSON
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	header('Content-Type: application/json');
	echo json_encode(service_get_data());
}


function service_get_data()
{

  // The user is trying to log in 
  if (isset($_POST['ra_login_email'])) {
    $query_statement = 'select uid from users where email="' . sanitize($_POST['ra_login_email']) . '"';
    $query = db()->prepare($query_statement);
    $query->execute();
    
    $data = $query->fetch(PDO::FETCH_ASSOC);
    
    if (isset($data['uid'])) {
      set_current_user($data['uid']);
    }
  }




  $user = current_user();

 
  if (!isset($user)) {
    return null;
  }

  $data = array();

  $profile_id_sanitized = sanitize($user);
  $query_statement = "select * from users where uid='$profile_id_sanitized'";

  $query = db()->prepare($query_statement);
  $query->execute();


  $data = $query->fetch(PDO::FETCH_ASSOC);

  return $data;

  
}

?>