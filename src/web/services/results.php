<?php
/**
 * Search web service
 * 
 * @author Laure Thompson <laurejt@cs.washington.edu>
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');
require_once 'includes/common.php';

// [PROFILING] Special debug flag to enable performance measurements
// TODO: remove this in production
if (isset($_GET['enableProfiling']))
{
	define('PROFILING', true);
}

// If file is requested directly, return the service's data encoded in JSON
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	header('Content-Type: application/json');
	echo json_encode(service_get_results($_GET['isGroup'], $_GET['id']));
}

//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

// [PROFILING] Maps task names to start/end times. Array structure looks like this:
//   $profiling_info['task_name']['start' | 'end']
$profiling_info = array();

function start_timer($task_name)
{
	global $profiling_info;
	$profiling_info[$task_name]['start'] = microtime(true);
}

function stop_timer($task_name)
{
	global $profiling_info;
	$profiling_info[$task_name]['end'] = microtime(true);
}

function dump_profiling_info()
{
	global $profiling_info;
	
	foreach ($profiling_info as $task => $times)
	{
		echo "$task: " . ($times['end'] - $times['start']) . "\n";
	}
	echo "\n";
}

/**
 * TODO: document this
 *
 * @return array An associative array of data for the view to display.
 */
function service_get_results($isGroupParam, $id)
{
	if (defined('PROFILING'))
	{
		start_timer('service_get_results');
	}
	
	$isGroup = $isGroupParam == "true" ? true : false;
	
	if (defined('PROFILING'))
	{
		start_timer("\tservice_get_preferences");
	}
	
	// Return the preferences for the group or user
	$preferences = service_get_preferences($isGroup, $id);
	
	if (defined('PROFILING'))
	{
		stop_timer("\tservice_get_preferences");
	}
	
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
		
		if (defined('PROFILING'))
		{
			start_timer("\tgroup members");
		}

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
		
		if (defined('PROFILING'))
		{
			stop_timer("\tgroup members");
			start_timer("\tcalculating scores");
			echo 'Calculating scores for ' . count($rids) . " restaurants...\n";
			$times = array();
		}

		//for ($i = 0; $i < 5; $i++) 
		//{
		//	$rid = $rids[$i];
		foreach ($rids as $rid) {
			$score = 0;
			
			if (defined('PROFILING'))
			{
				$start = microtime(true);
			}
			
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

			if (defined('PROFILING'))
			{
				$times[] = microtime(true) - $start;
			}
		}
	} else
	{
		if (defined('PROFILING'))
		{
			echo 'Calculating scores for ' . count($rids) . " restaurants...\n";
			start_timer("\tcalculating scores");
			$times = array();
		}
		//for ($i = 0; $i < 5; $i++) 
		//{
		foreach ($rids as $rid) {
			//$results[] = service_get_restaurant_score($id, $rid);
			
			if (defined('PROFILING'))
			{
				$start = microtime(true);
			}

			$score = service_get_restaurant_score($id, $rid/*s[$i]*/, $preferences);
			if ($score != NULL) 
			{
				$results[] = $score;
			}

			if (defined('PROFILING'))
			{
				$times[] = microtime(true) - $start;
			}
		}	
	}
	
	if (defined('PROFILING'))
	{
		stop_timer("\tcalculating scores");
		echo 'Found ' . count($results) . " restaurants.\n";
		echo 'Average time to calculate restaurant score: ' . (array_sum($times) / count($times)) . "\n\n";
		start_timer("\taggregating results");
	}
		
	foreach ($results as $key => $row) 
	{
		$rid[$key]  = $row['rid'];
		$scores[$key] = $row['score'];
	}
	
	// Sort the data with score descending, edition ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($scores, SORT_DESC, $results);
	
	if (defined('PROFILING'))
	{
		stop_timer("\taggregating results");
		start_timer("\tfetch names");
	}

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
	
	if (defined('PROFILING'))
	{
		stop_timer("\tfetch names");
		stop_timer('service_get_results');
		dump_profiling_info();
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
	$category_ratings = array();
	$foods = array();
	$food_ratings = array();
	$queryId = array($id);
	
	if (defined('PROFILING'))
	{
		start_timer("\t\tfetch category preferences");
	}
	
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
	
	if (defined('PROFILING'))
	{
		stop_timer("\t\tfetch category preferences");
		start_timer("\t\tfetch food preferences");
	}
	
	// Fetch food preferences
	if ($isGroup) {
		$query = db()->prepare('select up.fid, up.rating, u.uid from user_pref_foods up, group_members gm, users u where gm.uid = up.uid and gm.gid=? and u.uid=gm.uid order by up.uid');
	} else {
		$query = db()->prepare('select up.fid, up.rating, u.uid from users u, user_pref_foods up where u.uid = ? and up.uid=u.uid');
	}
	$query->execute($queryId);
	$results = $query->fetchAll();
	
	foreach ($results as $food) {
		$foods[] = $food['fid'];
		$food_ratings[$food['uid']][$food['fid']] = $food['rating'];
		//$food_ratings[$food['fid']] = $food['rating'];
	}
	
	if (defined('PROFILING'))
	{
		stop_timer("\t\tfetch food preferences");
		start_timer("\t\tfilter rids on categories");
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
	
	if (defined('PROFILING'))
	{
		stop_timer("\t\tfilter rids on categories");
		start_timer("\t\tfilter rids on attrs");
	}
	
	foreach ($foods as $fid) {
		$query = db()->prepare('select rid from restaurant_attributes ra where ra.aid = (select f.aid from foods f where f.fid  = ?)');
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
	
	if (defined('PROFILING'))
	{
		stop_timer("\t\tfilter rids on attrs");
	}
	
	return array($categories, $category_ratings, $foods, $food_ratings, $rids);
}

/***
 * 
 */
function service_get_restaurant_score($uid, $rid, $preferences)
{
	if (defined('PROFILING'))
	{
		start_timer("\tservice_get_restaurant_score");
	}

	$categories = $preferences[0];
	$category_ratings = $preferences[1];
	$foods = $preferences[2];
	$food_ratings = $preferences[3];	
	
	if (defined('PROFILING'))
	{
		start_timer("\t\tpolarity scoring");
	}

	// Get restaurant polarity scoring component
	$query = db()->prepare('Select polarity from restaurants where rid = ?');
	$query->execute(array($rid));
	$result = $query->fetch();
	$res_polarity = $result['polarity'];
	
	if (defined('PROFILING'))
	{
		stop_timer("\t\tpolarity scoring");
	}

	// skip restaurant if polarity = 0
	if ($res_polarity == 0) {
		return NULL;
	}

	if (defined('PROFILING'))
	{
		start_timer("\t\tcategory scoring");
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
	
	if (defined('PROFILING'))
	{
		stop_timer("\t\tcategory scoring");
		start_timer("\t\tfood scoring");
	}

	// Get food scoring component
	$max_food_score = 0;
	foreach ($foods as $fid)
	{
		// See if restaurant has corresponding attribute
		$sql = 'SELECT polarity from restaurant_attributes ra where ra.aid = (select f.aid from foods f where f.fid = ?)';
		$query = db()->prepare($sql);
		$query->execute(array($fid));
		
		if ($query->rowCount() > 0)
		{
			$food_polarity = $query->fetch();
			$rating = $food_ratings[$uid][$fid];
			$max_food_score = max($max_food_score, ($rating * 0.2 * $food_polarity['polarity']));
		}
	}

	if (defined('PROFILING'))
	{
		stop_timer("\t\tfood scoring");
		stop_timer("\tservice_get_restaurant_score");
	}
	
	return array('rid' => $rid, 'score' => 40*$max_cat_score + 40*$max_food_score + 20*$res_polarity);	
}

?>
