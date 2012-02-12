<?php
/*
 * Counts the number of reviews for restaurants the given review data (in JSON),
 * and adds them to our retaurant database. Requires the 'raw_restaurant_names'
 * table, which maps the slugs Yelp uses to identify restaurants to the ID numbers
 * we use to identify restaurants, in addition to the normal restaurant table.
 * 
 * This should be used with review data from the RevMiner snapshot from 2012-02-06,
 * since the given JSON file must have each restaurant group delineated by a line
 * break in order for this to work.
 *
 * Run this in shell mode from the tools directory:
 *   php count_reviews.php reviews.json
 *
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */
require_once '../src/web/includes/functions.php';  // Don't use common.php; it has session stuff
define('DEBUG', true);

// Processes one line of JSON, which is expected to be in the following format:
//
// {"restaurant-slug": {<review>, <review>, ...}}
//
// Given this, the function will update the restaurant entry in our database with
// the number of reviews it has.
function process_restaurant_reviews($rev_json)
{
	$rev_data = json_decode($rev_json, true);
	$restaurant_name = key($rev_data);  // Each restaurant will have only one key: its name
	$rid = get_rid($restaurant_name);
	
	if ($rid != NULL)
	{
		// Update restaurant row in our database with the review count
		$num_reviews = count($rev_data[$restaurant_name]);
		
		$sql = 'update restaurants set reviews = ? where rid = ?';
		$query = db()->prepare($sql);
		$query->execute(array($num_reviews, $rid));
	}
}

// Returns the restaurant ID associated with the given Yelp slug, or NULL if the
// given Yelp slug doesn't exist in the database.
function get_rid($slug)
{
	$sql = 'select rid from raw_restaurant_names where name = ?';
	$query = db()->prepare($sql);
	$query->execute(array($slug));

	if ($query->rowCount() > 0)
	{
		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row['rid'];
	}
	else
	{
		return NULL;  // Restaurant does not exist in our database
	}
}

//
// main
//

// Check input args
if ($argc != 2)
	die("Usage: php populate_restaurants.php reviewdata.json\n");

if (!is_file($argv[1]))
	die("Could not open JSON input file '{$argv[1]}'.\n");

// Parse the JSON file line-by-line, since each line corresponds to a set of
// reviews for one restaurant.
$rev_file = fopen($argv[1], 'r');

while ($rev_json = fgets($rev_file))
	process_restaurant_reviews($rev_json);

fclose($rev_file);

?>