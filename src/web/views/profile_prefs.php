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


	//print_r($data['categories']);

	if (isset($data)) {
	?>
    
		<a href="#" id="add_category_link" onclick="display_add_category();">Add new category preference</a><br />
		<div id="add_category" class="category hidden">
			
			
			Category: <!--<input name="category" type="text" /> <br /> -->
			<select name="category">
			<?php
				$categories = $data['categories'];
				foreach ($categories as $cat) {
				?>
					<option value="<?= $cat['cat_id'] ?>"><?= $cat['name'] ?></option>
				<?php
				}		
			?>
			</select> <br />
		  
		  
			Rating: 
			<input type="radio" name="rating" value="1" /> 1
			<input type="radio" name="rating" value="2" /> 2
			<input type="radio" name="rating" value="3" /> 3
			<input type="radio" name="rating" value="4" /> 4
			<input type="radio" name="rating" value="5" /> 5
			<br />
			<button type="button" onclick="add_category();">Add or update category</button>
		</div>


		<p>Categories:</p>
		<table border=1 id="preference_table">
			<tr><th>Food type</th><th>Rating</th></tr>
			<?php    

			//print_r($data);
			$user_prefs = $data['user_prefs'];
			foreach ($user_prefs as $pref) {
			?>
				<tr id="pref_<?= $pref['name'] ?>"><td class="cat_name"><?= $pref['name'] ?></td><td class="rating"><?= $pref['rating'] ?></td></tr>
			<?php
			}
			?>
		</table>
		<a href="profile.php">Back</a>
		<?php


	} else {
	?>
    <p>No preferences set!</p>
	<?php
	}
}