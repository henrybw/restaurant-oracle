<?php
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'includes/common.php';

$data = array();

$data['success'] = true;

echo json_encode($data);


?>