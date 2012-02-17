<?php
/**
 * Results front-end controller.
 * 
 *  @author Laure Thompson <laurejt@cs.washington.edu>
 */

require_once 'includes/common.php';
require_once 'services/results.php';
require_once 'views/results.php';

// Get group/user id from post form
$group = $_GET['group'];
$isGroup = $_GET['isGroup'];

page_header('Results');
page_body(service_get_results($isGroup, $group));
page_footer();

?>
