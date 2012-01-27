<?
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'includes/common.php';








if (isset($_POST['email']) && isset($_POST['fname']) && isset($_POST['lname'])) {

  $email = $_POST['email'];
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  
  
  db()->beginTransaction();
  
  $query = db()->prepare('insert into users(email,fname,lname) values(:email, :fname, :lname)');
  $query->bindParam(':email', $email);
  $query->bindParam(':fname', $fname);
  $query->bindParam(':lname', $lname);
  $query->execute();
  
  $user = db()->lastInsertId();
  set_current_user($user);


  db()->commit();
  
      
  $data = array();
  $data['foo'] = 'bar';
  $data['user'] = $user;

  echo json_encode($data);
} else {
  echo "missing parameters";
}