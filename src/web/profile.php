<?
/**
 * Displays a profile
 */

require_once 'includes/common.php';
require_once 'services/profile.php';
require_once 'views/profile.php';

page_header('Profile');
page_body(service_get_data($_GET['id']));
page_footer();

?>