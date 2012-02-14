<?php
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'includes/common.php';

$data = array();

if (isset($_POST['groupId']) && current_user()) {
	
	$group_id = $_POST['groupId'];
	
	
	// check to see that the user is already in this group
	$query_user_check = db()->prepare('select * from group_members ' .
		'where gid = :group_id and uid = :user_id');
	$query_user_check->bindParam(':group_id', $group_id);
	$query_user_check->bindParam(':user_id', current_user());
	$query_user_check->execute();
	
	if ($query_user_check->rowCount() > 0) {
		// The user is already in the group, return an error message
		
		$data['success'] = false;
		$data['error'] = "The user is already a member of this group";		
	} else {
		// The user isn't already in the group, so add them
	
		db()->beginTransaction();
		$query_add_user = db()->prepare('insert into group_members(gid,uid) values(:group_id, :user_id)');
		$query_add_user->bindParam(':group_id', $group_id);
		$query_add_user->bindParam(':user_id', current_user());
		$query_add_user->execute();
		db()->commit();
		
		$data['success'] = true;	
		$data['groupId'] = $group_id;
	}
	
} else {
	// We don't have the necessary data, so fill our data
	// object with error code.
	
	$data['success'] = false;
	
	if (!isset($_POST['groupId'])) {
		$data['message'] = 'Group id not set';
	} else if (!current_user()) {
		$data['message'] = 'Current user not set';
	}
}

echo json_encode($data);


?>