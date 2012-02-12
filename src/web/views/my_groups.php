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
	<h2>Groups you belong to:</h2>
	
	<table cellpadding="0" cellspacing="0" id="groups_table">
		<tr>
			<th><div class="corner left"></div></th>
			<th class="top">Group Id</th>
			<th class="top">Group Name</th>
			<th><div class="corner right"></div></th>
		</tr>
		<?php
		$even = true;
		foreach ($data as $group) {
		?>
			<tr class="<?= $even ? 'even' : 'odd' ?>">
				<td></td>
				<td><?= $group['gid'] ?></td>
				<td><?= $group['name'] ?></td>
				<td></td>
			</tr>
		<?php
			$even = !$even;
		}
		?>
		
		<tr class="bottom">
			<td></td>
			<td></td>
			<td><div></div></td>
			<td></td>
		</tr>
	</table>
	
	<a href="#" class="button action">Join a Group</a>
	<a href="#" class="button action">Create a Group</a>
	
	<a href="profile.php" class="button navBtn" >Back</a>
<?php
}
?>