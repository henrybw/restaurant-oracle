<?
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
	<p>This is the page for managing your profile and groups.</p>
		<!--<p>A link from the index page should say 'Manage Profiles and Groups'</p>-->
		<!-- <p>CURRENT USER IS: <?php print_r($_SESSION); ?></p>-->
		<!-- <p>DATA IS: <?php print_r($data) ?></p>-->
	<?php
	if (isset($data)) {
		// If the data is set, that means the user is logged in
	?>
		<p>
			Profile id: <?= $data['uid'] ?> <br />
			First name: <?= $data['fname'] ?><br />
			Last name: <?= $data['lname'] ?><br />
			Email address: <?= $data['email'] ?>
		</p>

		<p>
			<a href="profile_prefs.php" class="button action">Manage my Preferences</a>
			<!--<li><a href="#">Switch profiles</li>-->
			<a href="my_groups.php" class="button action">Manage My Groups</a>
		</p>
	<?php
	} else {
		// The user is not logged in, display a login page and option to create a new account
	?>
	
		<p>
			<div id="login">
				<a href="#" id="loginDisplayLink" onclick="toggleDropdown('login');" class="button dropDown">
					► ▼ Log in
				</a>
				
				<div id="loginDetails" class="login hidden dropDownDetails">
					<form action="profile.php" id="login_form" method="POST">
						Email address: <input name="ra_login_email" type="text" />
					</form>
					
					<a href="#" class="button submit" onclick="login_submit();">Go</a>
					<div class="clear"></div>
				</div>
			</div>
			
			
			
			
			<a href="create_profile.php" class="button action">Create a new profile</a><br />
			<!--<a href="#">Create a new group</a>-->
		</p>
<?php
	}
}
?>