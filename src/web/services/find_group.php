<?php
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'includes/common.php';

$data = array();



if (isset($_POST['name']) && current_user()) {
	
	$group_name = $_POST['name'];
	
	$query_search = db()->prepare("select * from groups where name like ?");
	// $query_search->bindParam(':group_name', "'%$group_name%'");
	$query_search->execute(array('%' . $group_name . '%'));
	
	
	
	$data['success'] = true;
	$data['groups'] = $query_search->fetchAll(PDO::FETCH_ASSOC);	
	
} else {
	// We don't have the necessary data, so fill our data
	// object with error code.
	
	$data['success'] = false;
	
	if (!isset($_POST['name'])) {
		$data['message'] = 'Group name not set';
	} else if (!current_user()) {
		$data['message'] = 'Current user not set';
	}
}

echo json_encode($data);


?>