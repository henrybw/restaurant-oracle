<?php
/**
 * Search page view.
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
	
	<div id="searchQuery">
		<input type="radio" name="searchType" value="individual" id="searchIndividual" checked/>
		<label for="searchIndividual">Individual</label>
		
		<input type="radio" name="searchType" value="group" id="searchGroup" />
		<label for="searchGroup">Group</label>
		
		<select name="group">
		<?php
			foreach ($data as $group) {
				?>
				<option value="<?= $group['gid'] ?>"><?= $group['name'] ?></option>
				
				<?php
			
			}
		?>
		
		</select>
		
		<!-- HACKITY HACK HACK -->
		<label></label>

		<input type="checkbox" id="reservations" />
		<label for="reservations">Must take reservations</label>
		
		<input type="checkbox" id="acceptsCreditCards" />
		<label for="acceptsCreditCards">Must take credit cards</label>
		
		<input type="checkbox" id="excludeClosed" />
		<label for="excludeClosed">Exclude closed restaurants</label>
		
		<input type="checkbox" id="excludeUnknownHours" />
		<label for="excludeUnknownHours">Exclude restaurants with unknown hours</label>

		<label for="priceRange">Price Range</label>
		
		<select name="priceRange">
			<option value="1">Under $10</option>
			<option value="2">Under $30</option>
			<option value="3">Under $60</option>
			<option value="4">Above $61</option>
		</select>
		
		<label for="distance">Distance (miles)</label>
		<input type="text" id="distance" value="1" />
		
		<a class="button submit" onclick="getSearchResults(<?= current_user() ?>);">Search</a>
	</div>
	
	<div id="resultsTable">
	
	</div>		
	
	
<?php
}
?>
