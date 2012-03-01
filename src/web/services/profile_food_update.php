<?php
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'includes/common.php';

$fid = $_GET['fid'];
$rating = $_GET['rating'];
$user = current_user();

$data = array();

if (isset($fid) && isset($rating) && isset($user)) {
	
	db()->beginTransaction();
	
	$exists_query = db()->prepare('select * from user_pref_foods where uid = :uid and fid = :fid');
	$exists_query->bindParam(':uid', $user);
	$exists_query->bindParam(':fid', $fid);
	$exists_query->execute();
	
	if ($exists_query->rowCount() > 0) {
		$query = db()->prepare('update user_pref_foods set rating = :rating ' .
			'where uid = :uid and fid = :fid');
		$query->bindParam(':uid', $user);
		$query->bindParam(':fid', $fid);
		$query->bindParam(':rating', $rating);
		$query->execute();
		
		$data['update'] = 'updated';
	} else {
		$query = db()->prepare('insert into user_pref_foods(uid,fid,rating)' .
		   'values(:uid, :fid, :rating)');
		$query->bindParam(':uid', $user);
		$query->bindParam(':fid', $fid);
		$query->bindParam(':rating', $rating);
		$query->execute();
		
		$data['updated'] = 'new_val';
	}
	
	db()->commit();
	
	$query = db()->prepare('select food from foods where fid = :fid');
	$query->bindParam(':fid', $fid);
	$query->execute();
	
	$f = $query->fetch(PDO::FETCH_ASSOC);
	$data['food'] = $f['food'];
    $data['rating'] = $rating;
	
	
} else {
	$data['error'] = true;
	$data['message'] = "food, rating, or user is null";
}


echo json_encode($data);

?>