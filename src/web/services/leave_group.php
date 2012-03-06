<?php
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'includes/common.php';

$data = array();
$groupId = $_POST['groupId'];

if (isset($groupId) && current_user()) {
	
	$query = db()->prepare('delete from group_members where gid = :gid ' . 
		'and uid = :uid');
	$query->bindParam(':gid', $groupId);
	$query->bindParam(':uid', current_user());
	$query->execute();	
	
	$data['gid'] = $groupId;
	$data['success'] = true;
	
} else {
	$data['error'] = true;
	$data['message'] = "group id or current user is not set";
}

echo json_encode($data);

?>