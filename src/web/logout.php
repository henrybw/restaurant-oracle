<?php
/**
 * Logs out any currently logged in user.
 * 
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

require_once 'includes/common.php';
require_once 'services/logout.php';
require_once 'views/logout.php';

// This returns nothing, but we want to log out first before we generate
// the page header (so we don't include the Log Out link).
service_get_data();

page_header('Logged out');
page_body();
page_footer();

?>
