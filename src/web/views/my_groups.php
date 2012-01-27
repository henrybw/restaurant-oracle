<?php
session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');



// File should never be requested directly
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	include('404.shtml');
	die();
}

//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

function page_body($data = null)
{
?>
  <p>My Groups:</p>

  <table border=1 id="groups_table">
    <tr><th>Group Id</th><th>Group Name</th></tr>
<?php


    foreach ($data as $group) {
?>
    <tr><td><?= $group['gid'] ?></td><td><?= $group['name'] ?></td></tr>

<?php
  }

?>
  </table>
  <a href="profile.php">Back</a>
<?php

}