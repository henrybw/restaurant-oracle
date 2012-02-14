<?php

session_start();

/*
 * @author Coral Peterson
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');


function page_body($data = null)
{
?>

	<div id="joinGroup">
		Group name: <input type="input" name="gname" /><br />
		
		<a href="#" class="button submit" onclick="findGroup();">Find Groups</a>
		<div class="clear"></div>
	</div>
	
	<div id="groupList">
	</div>

<?php
} 
?>