<?
session_start();

/*
 * @author Coral Peterson
 */

require_once 'includes/common.php';
require_once 'services/profile_prefs.php';
require_once 'views/profile_prefs.php';

page_header('Manage Preferences');
page_body(service_get_data());
page_footer();

?>