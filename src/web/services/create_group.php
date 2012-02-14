<?
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'includes/common.php';




$data = array();

if (isset($_POST['name']) && current_user()) {
	
	$group_name = $_POST['name'];
	
	db()->beginTransaction();
	
	$query_name_check = db()->prepare('select * from groups where name = :name');
	$query_name_check->bindParam(':name', $group_name);
	$query_name_check->execute();
	
	if ($query_name_check->rowCount() > 0) {
		// // this name already exists. rollback
		db()->rollBack();
		
		$data['success'] = false;
		$data['error'] = "Error: group name already exists";
	} else {
		
		$query_update = db()->prepare('insert into groups(name) values(:group_name)');
		$query_update->bindParam(':group_name', $group_name);
		$query_update->execute();
		
		$group_id = db()->lastInsertId();
		
		db()->commit();
		
		// v not working
		// Begin a new transaction so the foreign key constraints will work
		db()->beginTransaction();
		$query_add_user = db()->prepare('insert into group_members(gid,uid) values(:group_id, :user_id)');
		$query_add_user->bindParam(':group_id', $group_id);
		$query_add_user->bindParam(':user_id', current_user());
		$query_add_user->execute();
		db()->commit();
		// ^ not working
		
		$data['success'] = true;
		$data['group_id'] = $group_id;
		$data['group_name'] = $group_name;
		$data['current_user'] = current_user();
		$data['query'] = $query_add_user;
	}
		
} else {
	$data['success'] = false;
	$data['error'] = "missing parameters: user is not logged in or group name is invalid";
}

echo json_encode($data);

?>