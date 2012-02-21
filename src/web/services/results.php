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
function service_get_results($isGroupParam, $id)
{
	$isGroup = $isGroupParam == "true" ? true : false;
	
	// Return the preferences for the group or user
	$preferences = service_get_preferences($isGroup, $id);	
	$rids = $preferences[4];

	//print_r($preferences[0]);
	//print_r($preferences[1]);
	//print_r($preferences[2]);
	//print_r($preferences[3]);
	//print_r($preferences[4]);

	//return $preferences;

	$results = array();
	// Find three restaurants with best scores
	if ($isGroup)
	{
		$uids = array();
		// get list of group members
		$sql = 'Select uid from group_members where gid = ?';
		$query = db()->prepare($sql);
		$query->execute(array($id));
		$a = $query->fetchAll();
		
		foreach ($a as $key => $row)
		{
			$uids[] = $row['uid'];
		}
		
		$user_count = count($uids);
		//print 'user_count ' . $user_count . "\n";
		
		for ($i = 0; $i < 5; $i++) 
		{
			$rid = $rids[$i];
		//foreach ($rids as $rid) {
			$score = 0;
			foreach ($uids as $user) {
				//print 'user id = ' . $user . "\n";
				$score_part = service_get_restaurant_score($user, $rid, $preferences);
				if ($score_part != NULL) 
				{
					//print 'score part = '. $score_part['score'] . "\n";
					$score = $score + ($score_part['score'] / $user_count);
					//print 'accumulative score = ' . $score . "\n";
				}
			}
			if ($score != 0) {
				$results[] = array('rid' => $rid, 'score' => $score);
			}
		}
	} else
	{
		for ($i = 0; $i < 5; $i++) 
		{
		//foreach ($rids as $rid) {
			//$results[] = service_get_restaurant_score($id, $rid);
			
			$score = service_get_restaurant_score($id, $rids[$i], $preferences);
			if ($score != NULL) 
			{
				$results[] = $score;
			}
		}	
	}
			
	foreach ($results as $key => $row) 
	{
		$rid[$key]  = $row['rid'];
		$scores[$key] = $row['score'];
	}

	// Sort the data with score descending, edition ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($scores, SORT_DESC, $results);
	
	$top_three = array($results[0], $results[1], $results[2]);
	
	$top_restaurants = array();
	// Get result name for each rid
	foreach ($top_three as $key => $row)
	{
		$rid = $row['rid'];
		$query = db()->prepare('select name from restaurants where rid = ?');
		$query->execute(array($rid));
		$result = $query->fetch();
		
		$top_restaurants[] = array('rid' => $row['rid'], 'name' => $result['name'], 'score' => $row['score']);	
	}
	//print_r($top_restaurants);
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
	$category_ratings = array();
	$foods = array();
	$food_ratings = array();
	$queryId = array($id);
	
	// Fetch category preferences
	if ($isGroup) {
		$query = db()->prepare('select up.category, up.rating, u.uid from user_pref_categories up, group_members gm, users u where gm.uid = up.uid and gm.gid=? and u.uid=gm.uid order by up.uid');
	} else {
		$query = db()->prepare('select up.category, up.rating, u.uid from users u, user_pref_categories up where u.uid = ? and up.uid=u.uid');
	}
	$query->execute($queryId);
	$results = $query->fetchAll();
	
	foreach ($results as $cid) {
		$categories[] = $cid['category'];
		$category_ratings[$cid['uid']][$cid['category']] = $cid['rating'];
	}
	
	// Fetch food preferences
	if ($isGroup) {
		$query = db()->prepare('select up.food, up.rating, u.uid from user_pref_foods up, group_members gm, users u where gm.uid = up.uid and gm.gid=? and u.uid=gm.uid order by up.uid');
	} else {
		$query = db()->prepare('select up.food, up.rating, u.uid from users u, user_pref_foods up where u.uid = ? and up.uid=u.uid');
	}
	$query->execute($queryId);
	$results = $query->fetchAll();
	
	foreach ($results as $food) {
		$foods[] = $food['food'];
		$food_ratings[$food['uid']][$food['food']] = $food['rating'];
		//$food_ratings[$food['food']] = $food['rating'];
	}
	
	// Get restaurant ids
	$rids = array();
	foreach ($categories as $cid) {
		$query = db()->prepare('select rid from restaurant_categories where cid = ?');
		$query->execute(array($cid));
		$results = $query->fetchAll();
		
		foreach ($results as $row)
		{
			$rids[] = $row['rid'];
		}
	}
	
	foreach ($foods as $fid) {
		$query = db()->prepare('select rid from restaurant_attributes ra where ra.aid = (select f.aid from foods f where f.food  = ?)');
		$query->execute(array($fid));
		$results = $query->fetchAll();
			
		foreach ($results as $row)
		{
			$rids[] = $row['rid'];
		}
	}	
	// Sort and remove duplicates from restaurant id collection
	$rids = array_unique($rids);
	sort($rids);
	
	return array($categories, $category_ratings, $foods, $food_ratings, $rids);
}

/***
 * 
 */
function service_get_restaurant_score($uid, $rid, $preferences)
{
	$categories = $preferences[0];
	$category_ratings = $preferences[1];
	$foods = $preferences[2];
	$food_ratings = $preferences[3];	
	
	// Get restaurant polarity scoring component
	$query = db()->prepare('Select polarity from restaurants where rid = ?');
	$query->execute(array($rid));
	$result = $query->fetch();
	$res_polarity = $result['polarity'];
	
	// skip restaurant if polarity = 0
	if ($res_polarity == 0) {
		return NULL;
	}
	
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
			$rating = $category_ratings[$uid][$cid['cid']];
			$max_cat_rating = max($max_cat_rating, $rating);
		}
	}
	$max_cat_score = 0.2*$max_cat_rating;
	
	// Get food scoring component
	$max_food_score = 0;
	foreach ($foods as $food)
	{
		// See if restaurant has corresponding attribute
		$sql = 'SELECT polarity from restaurant_attributes ra where ra.aid = (select f.aid from foods f where food = ?)';
		$query = db()->prepare($sql);
		$query->execute(array($food));
		
		if ($query->rowCount() > 0)
		{
			$food_polarity = $query->fetch();
			$rating = $food_ratings[$uid][$food];
			$max_food_score = max($max_food_score, ($rating * 0.2 * $food_polarity['polarity']));
		}
	}
	
	return array('rid' => $rid, 'score' => 40*$max_cat_score + 40*$max_food_score + 20*$res_polarity);	
}

?>
