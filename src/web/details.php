<?php
/**
 * Displays detailed information for a given restaurant.
 * 
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

require_once 'includes/common.php';
require_once 'services/details.php';
require_once 'views/details.php';

// Only query the service if we are given an id
if (verify_args($_GET, array('id')))
{
	$id = (int)$_GET['id'];
	$data = service_get_data($id);
}

// $data will be null both if we couldn't find the restaurant or if
// no ID was provided.
$page_title = ($data) ? $data['name'] : 'Restaurant Not Found';

// Generate the page
page_header($page_title);
page_body($data);
page_footer();

?>
