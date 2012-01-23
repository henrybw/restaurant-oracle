<?php
/**
 * Functions to generate common page elements (header, footer).
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
		<title>Restaurant Oracle :: <?= $title ?></title>
		<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
	</head>
	<body>
		<div id="header">
			<h1>Restaurant Oracle</h1>
		</div>
		<div id="nav">
			NAV STUFFS GOES HERE
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
			TODO: COPYRIGHT STUFFS
		</div>
	</body>
</html>
<?php
}