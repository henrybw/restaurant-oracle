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

	<div id="createGroup">
		Group name: <input type="input" name="gname" /><br />
		
		<a href="#" class="button submit" onclick="createGroup();">Create Group</a>
		<div class="clear"></div>
	</div>
<?php
}
?>