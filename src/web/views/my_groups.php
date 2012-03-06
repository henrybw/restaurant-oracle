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
	
	<?php
	
	if (count($data) > 0) {
	?>
		<table cellpadding="0" cellspacing="0" id="groups_table">
			<tr>
				<th class="corner"><div class="left"></div></th>
				<th class="top">Group Id</th>
				<th class="top">Group Name</th>
				<th class="top"></th>
				<th class="corner"><div class="right"></div></th>
			</tr>
			<?php
			$even = true;
			foreach ($data as $group) {
			?>
				<tr class="<?= $even ? 'even' : 'odd' ?>">
					<td></td>
					<td><?= $group['gid'] ?></td>
					<td><?= $group['name'] ?></td>
					<td><a class="button submit" onclick="leaveGroup(<?= $group['gid'] ?>)">X</a></td>
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
				<td></td>
			</tr>
		</table>
	<?php
	} else {
	?>
		<p>You have not joined any groups</p>
	<?php
	}
	?>
	<a href="join_group.php" class="button action">Join a Group</a>
	<a href="create_group.php" class="button action">Create a Group</a>
	
	<!--<a href="profile.php" class="button navBtn" >Back</a>-->
<?php
}
?>