<?
session_start();

require_once 'includes/common.php';
require_once 'services/profile_prefs.php';
require_once 'views/profile_prefs.php';

page_header('Preferences');
page_body(service_get_data());
page_footer();

?>