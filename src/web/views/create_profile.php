<?php
session_start();

/*
 * @author Coral Peterson
 */


set_include_path(get_include_path() . PATH_SEPARATOR . '../');


//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

function page_body($data = null)
{
?>

<div id="createProfile">
	Email: <input type="input" name="email" /><br />
	First name: <input type="input" name="fname" /><br />
	Last name: <input type="input" name="lname" /><br />
	
	<a class="button submit" onclick="create_profile();">Create Profile</a>
	<div class="clear"></div>
</div>

<div id="create_profile_status" class="status hidden">

</div>

<?php


}
?>