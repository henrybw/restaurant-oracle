<?
session_start();

require_once 'includes/common.php';

set_include_path(get_include_path() . PATH_SEPARATOR . '../');






/*



  $email = $_POST['email'];
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];

  
  db()->beginTransaction();

  $query = db()->prepare('insert into users(email,fname,lname) values(:email, :fname, :lname)');
  $query->bindParam(':email', $email);
  $query->bindParam(':fname', $fname);
  $query->bindParam(':lname', $lname);
  $query->execute();

  db()->commit();
  

  $user = db()->lastInsertId();
  set_current_user($user);
*/


$data = array();
$data['foo'] = 'bar';

echo json_encode($data);