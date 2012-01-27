<?php
session_start();




set_include_path(get_include_path() . PATH_SEPARATOR . '../');


//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

function page_body($data = null)
{
?>

<div id="create_profile">
  Email: <input type="input" name="email" /><br />
  First name: <input type="input" name="fname" /><br />
  Last name: <input type="input" name="lname" /><br />

  <button type="button" onclick="create_profile();">Create profile</button>


</div>

<a href="profile.php">Back</a>

<?php


}
?>