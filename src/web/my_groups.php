<?
session_start();

/*
 * @author Coral Peterson
 */

require_once 'includes/common.php';
require_once 'services/my_groups.php';
require_once 'views/my_groups.php';

page_header('My Groups');
page_body(service_get_data());
page_footer();

?>