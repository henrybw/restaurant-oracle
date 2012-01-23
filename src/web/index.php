<?php
/**
 * Test web page.
 */

require_once 'includes/common.php';
require_once 'services/test.php';
require_once 'views/test.php';

page_header('Index');
page_body(service_get_data());
page_footer();

?>