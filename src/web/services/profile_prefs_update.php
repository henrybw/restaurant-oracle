<?php
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'includes/common.php';



$cat = $_POST['category'];
$rating = $_POST['rating'];
$user = $_SESSION['profile_id'];




  if (isset($cat) && isset($rating) && isset($user)) {

    db()->beginTransaction();


    $query = db()->prepare('insert into user_pref_categories(uid,category,rating)' .
			   'values(:uid, :category, :rating)');
    $query->bindParam(':uid', $user);
    $query->bindParam(':category', $cat);
    $query->bindParam(':rating', $rating);
    $query->execute();
    
    
    db()->commit();

    $to_return = array();
    $to_return['cat'] = $cat;
    $to_return['rating'] = $rating;


    echo json_encode($to_return);
    //echo "great success";
  } else {

    echo "missing arguments";
  }
?>