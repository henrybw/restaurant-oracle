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
		<label for="searchGroup">Group<label>
		
		<select name="group">
		
		</select>
		
		<a class="button submit" onclick="getSearchResults();">Search</a>
	</div>
	
<?php
}
?>
