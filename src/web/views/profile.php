<?
set_include_path(get_include_path() . PATH_SEPARATOR . '../');

// File should never be requested directly
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	include('../404.shtml');
	die();
}

//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

function page_body($data = null)
{
?>
  <p>This is the page for managing profiles and groups</p>
  <p>A link to the index page should say 'Manage Profiles and Groups'</p>

<?php
				     if (isset($data)) {
?>
  <p>
    Profile id: <?= $data['uid'] ?> <br />
    First name: <?= $data['fname'] ?><br />
    Last name: <?= $data['lname'] ?><br />
    Email address: <?= $data['email'] ?>
  </p>

  <p>
    <ul>
      <li><a href="#">Manage my restaurants and foods</a> (should be first, add once sessions work)</li>
      <li><a href="#">Switch profiles</li>
      <li><a href="#">Manage my groups</a></li>
    </ul>
  </p>


<?php
					 } else {
?>

<a href="#">Create a new profile</a>
<?php
					 }
?>



  <p>
    <ul>
      <li><a href="#">Create a new group</a></li>
      
    </ul>
  </p>

  <p>Hello world! Test data is: <?= print_r($data) ?></p>
<?php
}
?>