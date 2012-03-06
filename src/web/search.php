<?php
session_start();
/**
 * Search front-end controller.
 * 
 *  @author Laure Thompson <laurejt@cs.washington.edu>
 */

require_once 'includes/common.php';
require_once 'services/my_groups.php';
require_once 'views/search.php';

page_header('Search');
page_body(service_get_data());
page_footer();

?>
