<?php
/**
 * Search web service
 * 
 * @author Laure Thompson <laurejt@cs.washington.edu>
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');
require_once 'includes/common.php';

// If file is requested directly, return the service's data encoded in JSON
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	header('Content-Type: application/json');
	echo json_encode(service_get_results($_GET['isGroup'], $_GET['id']));
}

//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

/**
 * TODO: document this
 *
 * @return array An associative array of data for the view to display.
 */
function service_get_results($isGroupParam, $group)
{
	// Return the preferences for the group or user
	$preferences = service_get_preferences($isGroup, $group);	
	
	$categories = $preferences[0];
	$foods = $preferences[1];
	$rids = $preferences[2];
	
	 return $preferences;
	
	$isGroup = $isGroupParam == "true" ? true : false;
	
	
	$results = array();
	// Find three restaurants with best scores
	if ($isGroup)
	{
		$uids = array();
		// get list of group members
		$sql = 'Select uid from group_members where gid = ?';
		$query = db()->prepare($sql);
		$query->execute(array($group));
		$a = $query->fetchAll();
		
		foreach ($a as $key => $row)
		{
			$uids[] = $row['uid'];
		}
		
		$user_count = count($uids);
		
		foreach ($rids as $rid) {
			$score = 0;
			foreach ($uids as $user) {
				$score_part = service_get_restaurant_score($uid, $rid);
				$score = $score + ($score_part['score'] / $user_count);
			}
			$results[] = array('rid' => $rid, 'score ' => $score);
		}
	} else
	{
	
		foreach ($rids as $rid) {
			$results[] = service_get_restaurant_score($group, $rid);
		}	
		
		return $results;
		
	}
			
	foreach ($results as $key => $row) 
	{
		$rid[$key]  = $row['rid'];
		$score[$key] = $row['score'];
	}

	// Sort the data with score descending, edition ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($score, SORT_DESC, $results);
	
	$top_three = array($results[0], $results[1], $results[2]);
	
	return $top_three;
	
	$top_restaurants = array();
	// Get result name for each rid
	foreach ($top_three as $key => $row)
	{
		$rid = $row['rid'];
		$query = db()->prepare('select name from restaurants where rid = ?');
		$query->execute(array($rid));
		$result = $query->fetchAll();
		$top_restaurants[] = $result['name'];	
	}
	
	return $top_restaurants;
}

/**
 * TODO: document this
 *
 * @return array An associative array of data for the view to display.
 */
function service_get_preferences($isGroup, $id)
{
	$categories = array();
	$foods = array();
	$queryId = array($id);
	
	// Fetch category preferences
	if ($isGroup) {
		$query = db()->prepare('select up.category from user_pref_categories up, group_members gm, users u where gm.uid = up.uid and gm.gid=? and u.uid=gm.uid order by up.uid');
	} else {
		$query = db()->prepare('select up.category from users u, user_pref_categories up where u.uid = ? and up.uid=u.uid');
	}
	$query->execute($queryId);
	$categories = $query->fetchAll();
	
	// Fetch food preferences
	if ($isGroup) {
		$query = db()->prepare('select up.food from user_pref_foods up, group_members gm, users u where gm.uid = up.uid and gm.gid=? and u.uid=gm.uid order by up.uid');
	} else {
		$query = db()->prepare('select up.food from users u, user_pref_foods up where u.uid = ? and up.uid=u.uid');
	}
	$query->execute($queryId);
	$foods = $query->fetchAll();
	
	// Get restaurant ids
	$rids = array();
	foreach ($categories as $cid) {
		$query = db()->prepare('select rid from restaurant_categories where cid = ?');
		$query->execute(array($cid['category']));
		$results = $query->fetchAll();
		
		foreach ($results as $row)
		{
			$rids[] = $row['rid'];
		}
	}
	
	foreach ($foods as $fid) {
		$query = db()->prepare('select rid from restaurant_attributes ra where ra.aid = (select f.aid from foods f where f.food  = ?)');
		$query->execute(array($fid['food']));
		$results = $query->fetchAll();
			
		foreach ($results as $row)
		{
			$rids[] = $row['rid'];
		}
	}	
	$rids = array_unique($rids);
	
	return array($categories, $foods, $rids);
}

/***
 * 
 */
function service_get_restaurant_score($uid, $rid)
{
	$preferences = service_get_preferences(false, $uid);
	$categories = $preferences[0];
	$foods = $preferences[1];
	
	// Get category scoring component
	$sql = 'SELECT cid from restaurant_categories where rid = ?';
	$query = db()->prepare($sql);
	$query->execute(array($rid));
	$cat_ids = $query->fetchAll();
	
	$max_cat_rating = 0;
	foreach ($cat_ids as $cid)
	{
		if (in_array($cid['cid'], $categories))
		{
			// Get category preference rating
			$query = db()->prepare('Select rating from user_pref_categories WHERE uid = ? AND category = ?');
			$query->execute(array($uid, $cid['cid']));
			$rating = $query->fetch();
			
			$max_cat_rating = max($max_cat_rating, $rating['rating']);
		}
	}
	$max_cat_score = 0.2*$max_cat_rating;
	
	// Get food scoring component
	$max_food_score = 0;
	foreach ($foods as $fid)
	{
		// See if restaurant has corresponding attribute
		$sql = 'SELECT polarity from restaurant_attributes ra where ra.aid = (select f.aid from foods f where food = ?)';
		$query = db()->prepare($sql);
		$query->execute(array($fid['food']));
		
		if ($query->rowCount() > 0)
		{
			$food_polarity = $query->fetch();
			
			// Get food preference rating
			$query = db()->prepare('Select rating from user_pref_foods WHERE uid = ? AND food = ?');
			$query->execute(array($uid, $fid['food']));
			$rating = $query->fetch();
			
			$max_food_score = max($max_food_score, ($rating['rating'] * $food_polarity['polarity']));
		}
	}
	
	// Get restaurant polarity scoring component
	$query = db()->prepare('Select polarity from restaurants where rid = ?');
	$query->execute(array($rid));
	$res_polarity = $query->fetch();
	
	return array('rid' => $rid, $max_cat_rating, $max_food_score, $res_polarity['polarity']);
	//return array('rid' => $rid, 'score' => 0.4*$max_cat_rating + 0.4*$max_food_score + 0.2*$res_polarity['polarity']);	
}

?>
