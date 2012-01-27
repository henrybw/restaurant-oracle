<?php
/**
 * Results page view.
 * 
 * @author Laure Thompson <laurejt@cs.washington.edu>
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

/**
 * Generates the page body for this page.
 *
 * @param array $data Data for the page.
 */
function page_body($data = null)
{
	$user = current_user();
?>			
			<p>User ID = <?= $user ?></p>
			
			<p>RESULTS:</p>

			<table>
						<tr>
									<td> 
									Restaurant Id
									</td>
									
									<td>
									Restaurant Name
									</td>
						</tr>

<?php
			foreach ($data as $row) {
				
?>
				<tr>
							<td>
										<?= $row['rid'] ?>
							</td>
							
							<td>
										<a href="details.php?id=<?= $row['rid'] ?>"><?= $row['name'] ?> </a>
							</td>
				</tr>
<?php
			}
?>

	</table>

<?php
}
?>
