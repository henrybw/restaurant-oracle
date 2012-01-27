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

  // make sure profile_id exists
  if (isset($_POST['ra_login_email'])) {
    $query_statement = 'select uid from users where email="' . sanitize($_POST['ra_login_email']) . '"';
    $query = db()->prepare($query_statement);
    $query->execute();
    
    $data = $query->fetch(PDO::FETCH_ASSOC);
    
    if (isset($data['uid'])) {
      set_current_user($data['uid']);
     

      $data['test'] = 'data["test"] is set';
      return $data; //['uid']
    } else {
      $data['test'] = 'data["uid"] is not set';
      return $data;
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

  return null;
}

?>