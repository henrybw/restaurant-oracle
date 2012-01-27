<?
session_start();

require_once 'includes/common.php';
require_once 'services/profile.php';
require_once 'views/profile.php';

page_header('Profile');
page_body(service_get_data());
page_footer();

?>