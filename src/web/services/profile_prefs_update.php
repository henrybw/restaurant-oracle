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
	
	$to_return = array();
	
    db()->beginTransaction();

	//TODO: error checking
	
	$exists_query = db()->prepare('select * from user_pref_categories where uid = :uid and category = :cat');
	$exists_query->bindParam(':uid', $user);
	$exists_query->bindParam(':cat', $cat);
	$exists_query->execute();
	
	if ($exists_query->rowCount() > 0) {
		$query = db()->prepare('update user_pref_categories set rating = :rating ' .
			'where uid = :uid and category = :cat');
		$query->bindParam(':uid', $user);
		$query->bindParam(':cat', $cat);
		$query->bindParam(':rating', $rating);
		$query->execute();
		
		$to_return['update'] = 'updated';
		
		
	} else {
		$query = db()->prepare('insert into user_pref_categories(uid,category,rating)' .
				   'values(:uid, :category, :rating)');
		$query->bindParam(':uid', $user);
		$query->bindParam(':category', $cat);
		$query->bindParam(':rating', $rating);
		$query->execute();
		
		$to_return['updated'] = 'new_val';
    }
    
    db()->commit();

	
	$query = db()->prepare('select name from categories where cat_id = :cat_id');
	$query->bindParam(':cat_id', $cat);
	$query->execute();
	
    
    $to_return['cat'] = $query->fetch(PDO::FETCH_ASSOC);
    $to_return['rating'] = $rating;


    echo json_encode($to_return);
    //echo "great success";
  } else {

    echo "missing arguments";
  }
?>