<?php
/**
 * Functions to generate common page elements (header, footer).
 * 
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

// File should never be requested directly
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	include('../404.shtml');
	die();
}

/**
 * Generates and outputs the common page header. Opens up a main "div"
 * to work with.
 *
 * @param string $title The page title
 */
function page_header($title)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/> 
			<title>Restaurant Oracle :: <?= $title ?></title>
			<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyCe0k3vdXr5JZiJMjRQMbVnB3_mhne7Fqs"></script>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/profile.js"></script>
			<link rel="stylesheet" type="text/css" href="views/css/common.css" media="screen"/>
    <link rel="stylesheet" type="text/css" href="views/css/mobile.css" media="screen and (max-width: 480px)" />
                
	</head>
	<body>
		<div id="header">
			<h1>Restaurant Oracle</h1>
		
			<div id="nav">
				<a href="index.php">Home</a> |
				<a href="profile.php">Profile</a> |
				<a href="search.php">Search</a>
				<?= logged_in() ? '| <a href="logout.php">Log Out</a>' : '' ?>
				
			</div>
		</div>
		<div id="main">
<?php
}

/**
 * Generates and outputs the common page footer.
 */
function page_footer()
{
?>
		</div>
		<div id="footer">
			<p>
				Team Boxcat, 2012<br />
				Coral, Henry, Laure
			</p>
		</div>
	</body>
</html>
<?php
}
