<?
session_start();

/*
 * @author Coral Peterson
 */

require_once 'includes/common.php';
require_once 'services/profile.php';
require_once 'views/profile.php';

// Called before page_header() is called so the nav bar can be
// appropriately updated to correctly display "Log Out".
$data = service_get_data();

page_header('Profile');
page_body($data);
page_footer();

?>
