<?php
/**
 * Search web service
 * 
 * @author Laure Thompson <laurejt@cs.washington.edu>
 * Per-query filtering implemented by Henry Baba-Weiss <htw@cs.washington.edu>
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');
require_once 'includes/common.php';

// Constant for restaurants that have no open hours specified
define('HOURS_UNKNOWN', 'unknown');
define('HOURS_CLOSED', 'closed');
define('HOURS_OPEN', 'open');

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

	// Grab the filters from the GET parameters and aggregate them into one array
	$filter_info = array(
		'latitude' => (float)($_GET['latitude']),
		'longitude' => (float)($_GET['longitude']),
		'maxDistance' => (float)($_GET['maxDistance']),
		'reservations' => $_GET['reservations'],
		'acceptsCreditCards' => $_GET['acceptsCreditCards'],
		'price' => ((isset($_GET['price'])) ? (int)($_GET['price']) : 4),
		'excludeClosed' => $_GET['excludeClosed'],
		'excludeUnknownHours' => $_GET['excludeUnknownHours'],
		'currentTime' => ((int)$_GET['currentTime'] / 1000)  // In seconds
	);

	echo json_encode(service_get_results($_GET['isGroup'], $_GET['id'], $filter_info));
}

//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

// [PROFILING] Maps task names to start/end times. Array structure looks like this:
//   $profiling_info['task_name']['start' | 'end']
$profiling_info = array();

// [PROFILING] Maps groups of task names to start/end times. This is used for
// calculating statistics for a repeated task. Array structure looks like this:
//   $repeated_task['task_name'][iteration]['start' | 'end']
$repeated_tasks = array();

function start_timer($task_name, $iteration = -1)
{
	global $profiling_info, $repeated_tasks;
	
	if ($iteration >= 0)
	{
		$repeated_tasks[$task_name][$iteration]['start'] = microtime(true);
	}
	else
	{
		$profiling_info[$task_name]['start'] = microtime(true);
	}
}

function stop_timer($task_name, $iteration = -1)
{
	global $profiling_info, $repeated_tasks;
	
	if ($iteration >= 0)
	{
		$repeated_tasks[$task_name][$iteration]['end'] = microtime(true);
	}
	else
	{
		$profiling_info[$task_name]['end'] = microtime(true);
	}
}

function dump_profiling_info()
{
	global $profiling_info, $repeated_tasks;
	
	foreach ($profiling_info as $task => $times)
	{
		echo "$task: " . ($times['end'] - $times['start']) . "\n";
	}
	
	echo "\nRepeated tasks:\n";
	
	foreach ($repeated_tasks as $task => $times)
	{
		$max = 0;
		$sum = 0;
		
		for ($i = 0; $i < count($times); $i++)
		{
			$elapsed_time = $times[$i]['end'] - $times[$i]['start'];
			$max = max($max, $elapsed_time);
			$sum += $elapsed_time;
		}
		
		$avg = $sum / count($times);
		echo "$task: $avg (avg), $max (max)\n";
	}
	
	echo "\n";
}

/**
 * TODO: document this
 *
 * @return array An associative array of data for the view to display.
 */
function service_get_results($isGroupParam, $id, $filter_info)
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

	// Filter the restaurants based on per-query filter
	$sql = 'SELECT r.rid, r.name, r.price, r.latitude, r.longitude, ' .
	           'SQRT(POW(69.1 * (r.latitude - ?), 2) + POW(69.1 * (? - r.longitude) * COS(r.latitude / 57.3), 2)) AS distance ' .
	           'FROM restaurants AS r ' .
	       'WHERE r.rid IN (' . implode(',', $rids) . ') ';

	if ($filter_info['reservations'] == 'true')
		$sql .= 'AND r.reservations = 1 ';
	
	if ($filter_info['acceptsCreditCards'] == 'true')
		$sql .= 'AND r.accepts_credit_cards = 1 ';
   	
   	$sql .= 'AND r.price <= ? ';
	
	if ($filter_info['maxDistance'])
		$sql .= 'HAVING distance < ? ORDER BY distance';
	
	$params = array($filter_info['latitude'], $filter_info['longitude'], $filter_info['price']);

	if ($filter_info['maxDistance'])
		$params[] = $filter_info['maxDistance'];

	$query = db()->prepare($sql);
	$query->execute($params);

	if ($query->rowCount() < 1)
	{
		// TODO: There are no restaurants to search; return some kind of empty list.
		echo 'No restaurants found!';
		die();
	}

	$rids = array();
	$distances = array();
	$is_open = array();
	
	while ($row = $query->fetch())
	{
		$rids[] = $row['rid'];
		$distances[$row['rid']] = $row['distance'];
	}

	// Get open/closed information for the remaining restaurants
	$timestamp = $filter_info['currentTime'];
	$time = (int)((($timestamp - strtotime(date('Y-m-d 00:00:00', $timestamp))) + (86400 * (date('N', $timestamp) - 1))) / 60);

	foreach ($rids as $key => $rid)
	{
		$sql = 'SELECT rh.rid, rh.opening_time ' .
		       'FROM restaurant_hours rh ' .
		       'WHERE rid = ? AND ' .
		           '((? BETWEEN rh.opening_time AND rh.opening_time + rh.minutes_open) OR ' .
		           'rh.opening_time IS NULL)';
		$query = db()->prepare($sql);
		$query->execute(array($rid, $time));

		if ($query->rowCount() > 0)
		{
			$row = $query->fetch();

			if ($row['opening_time'])
				$is_open[$rid] = HOURS_OPEN;
			else
				$is_open[$rid] = HOURS_UNKNOWN;
		}
		else
		{
			$is_open[$rid] = HOURS_CLOSED;
		}

		// Filter if necessary
		if (($filter_info['excludeUnknownHours'] && $is_open[$rid] == HOURS_UNKNOWN) ||
			($filter_info['excludeClosed'] && $is_open[$rid] == HOURS_CLOSED))
		{
			unset($rids[$key]);
			unset($distances[$rid]);
			unset($is_open[$rid]);
		}
	}

	if (count($rids) < 1)
	{
		// TODO: There are no restaurants to search; return some kind of empty list.
		echo 'No restaurants found!';
		die();
	}

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
		
		if (defined('PROFILING'))
		{
			stop_timer("\tgroup members");
			start_timer("\tcalculating scores");
			echo 'Calculating scores for ' . count($rids) . " restaurants and $user_count users...\n";
			$i = 0;
		}

		//for ($i = 0; $i < 5; $i++) 
		//{
		//	$rid = $rids[$i];
		
		foreach ($rids as $rid) {
			$score = 0;
			
			if (defined('PROFILING'))
			{
				start_timer('get scores for users', $i);
			}
			
			foreach ($uids as $user) {
				//print 'user id = ' . $user . "\n";
				start_timer('service_get_restaurant_score', $i);
				$score_part = service_get_restaurant_score($user, $rid, $preferences, $i);
				stop_timer('service_get_restaurant_score', $i);
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
				stop_timer('get scores for users', $i);
				$i++;
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

			$score = service_get_restaurant_score($id, $rid/*s[$i]*/, $preferences, $i);
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
		echo 'Found ' . count($results) . " restaurants.\n\n";
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
		
		$top_restaurants[] = array(
			'rid' => $row['rid'],
			'name' => $result['name'],
			'score' => $row['score'],
			'distance' => $distances[$row['rid']],
			'isOpen' => $is_open[$row['rid']]
		);
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
		echo 'Categories to search: ' . count($categories) . "\n";
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
		echo 'Foods to search: ' . count($foods) . "\n";
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
 * [PROFILING] TODO: remove $i param (this is the current iteration)
 */
function service_get_restaurant_score($uid, $rid, $preferences, $i)
{
	$categories = $preferences[0];
	$category_ratings = $preferences[1];
	$foods = $preferences[2];
	$food_ratings = $preferences[3];	
	
	if (defined('PROFILING'))
	{
		start_timer("\tpolarity scoring", $i);
	}

	// Get restaurant polarity scoring component
	$query = db()->prepare('Select polarity from restaurants where rid = ?');
	$query->execute(array($rid));
	$result = $query->fetch();
	$res_polarity = $result['polarity'];
	
	if (defined('PROFILING'))
	{
		stop_timer("\tpolarity scoring", $i);
	}

	// skip restaurant if polarity = 0
	if ($res_polarity == 0) {
		return NULL;
	}

	if (defined('PROFILING'))
	{
		start_timer("\tcategory scoring", $i);
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
		stop_timer("\tcategory scoring", $i);
		start_timer("\tfood scoring", $i);
	}

	// Get food scoring component
	$max_food_score = 0;
	
	// See if restaurant has corresponding attribute
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
		stop_timer("\tfood scoring", $i);
	}
	
	return array('rid' => $rid, 'score' => 40*$max_cat_score + 40*$max_food_score + 20*$res_polarity);	
}

?>
