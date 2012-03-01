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
		
		
		<div id="addCategory">
			<a id="addCategoryDisplayLink" class="button dropDown" onclick="toggleDropdown('addCategory');">
				► ▼ Add New Category
			</a>			
			
			<div id="addCategoryDetails" class="dropDownDetails hidden">
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
			  
			  
				<span id="ratingBlock" class="ratings">
					Rating:
					<input type="radio" name="cat_rating" id="rating1" value="1" />
					<label for="rating1">1</label>
					<input type="radio" name="cat_rating" id="rating2" value="2" />
					<label for="rating2">2</label>
					<input type="radio" name="cat_rating" id="rating3" value="3" />
					<label for="rating3">3</label>
					<input type="radio" name="cat_rating" id="rating4" value="4" />
					<label for="rating4">4</label>
					<input type="radio" name="cat_rating" id="rating5" value="5" />
					<label for="rating5">5</label>
				</span>
				
				
				<a class="button submit" onclick="add_category();">Add or Update</a>
				
				
				<div class="clear"></div>
				
			</div>
			
		</div>
		
		
		<div id="addFood">
			<a id="addFoodDisplayLink" class="button dropDown" onclick="toggleDropdown('addFood');">
				► ▼ Add New Food
			</a>
			
			<div id="addFoodDetails" class="dropDownDetails hidden">
				Food:
				<select name="food">
				<?
					$foods = $data['foods'];
					foreach ($foods as $food) {
					?>
						<option value="<?= $food['fid'] ?>"><?= $food['food'] ?></option>
					<?
					}
				?>				
				</select>
				
				<span id="foodRatingBlock" class="ratings">
					Rating:
					<input type="radio" name="food_rating" id="rating1" value="1" />
					<label for="rating1">1</label>
					<input type="radio" name="food_rating" id="rating2" value="2" />
					<label for="rating2">2</label>
					<input type="radio" name="food_rating" id="rating3" value="3" />
					<label for="rating3">3</label>
					<input type="radio" name="food_rating" id="rating4" value="4" />
					<label for="rating4">4</label>
					<input type="radio" name="food_rating" id="rating5" value="5" />
					<label for="rating5">5</label>
				</span>
				
				<a class="button submit" onclick="addFood();">Add or Update</a>
				
				
				<div class="clear"></div>
			</div>
		</div>
		
		

		<h2>Categories:</h2>
		<table cellpadding="0" cellspacing="0" id="preferenceTable">
			<tr>
				<th class="corner"><div class="left"></div></th>
				<th class="top">Restaurant Type</th>
				<th class="top">Rating</th>
				<th class="corner"><div class="right"></div></th>
			</tr>
			<?php    

			//print_r($data);
			$user_prefs = $data['user_prefs'];
			$even = true;
			foreach ($user_prefs as $pref) {
			?>
				<tr id="pref_<?= $pref['name'] ?>" class="<?= $even ? 'even' : 'odd' ?>">
					<td></td>
					<td class="cat_name"><?= $pref['name'] ?></td>
					<td class="rating"><?= $pref['rating'] ?></td>
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
		
		
		
		<h2>Foods:</h2>
		
		<table cellpadding="0" cellspacing="0" id="foodTable">
			<tr>
				<th class="corner"><div class="left"></div></th>
				<th class="top">Food</th>
				<th class="top">Rating</th>
				<th class="corner"><div class="right"></div></th>
			</tr>
			
			<?    

			//print_r($data);
			$food_prefs = $data['food_prefs'];
			$even = true;
			print_r("food prefs: $food_prefs");
			foreach ($food_prefs as $pref) {
			?>
				<tr id="food_<?= $pref['name'] ?>" class="<?= $even ? 'even' : 'odd' ?>">
					<td></td>
					<td class="food_name"><?= $pref['name'] ?></td>
					<td class="food_rating"><?= $pref['rating'] ?></td>
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
		
		
		<?php


	} else {
	?>
    <p>No preferences set!</p>
	<?php
	}
}