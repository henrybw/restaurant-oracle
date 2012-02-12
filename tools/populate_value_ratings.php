<?php
/*
 * Fetches the frequency of star ratings for each value for the given
 * polarity data (in JSON), and adds them to our retaurant database. 
 *
 * Run this in shell mode from the tools directory:
 *   php count_reviews.php ../data/all.polarityData
 *
 * @author Laure Thompson <laurejt@cs.washington.edu>
 */
require_once '../src/web/includes/functions.php';  // Don't use common.php; it has session stuff
define('DEBUG', true);

// Processes JSON object, which is expected to be in the following format:
//
// {"value1": {<one_star>, <two_star>, <three_star>, <four_star>, <five_star>},  "value2":...}
//
// Given this, the function will update the value entry in our database with
// the frequency of each star rating.
function process_polarity_data($rev_json)
{
	$rev_data = json_decode($rev_json, true);

	foreach ($rev_data as $value => $polarities) 
	{
		// Update value row in our database with the corresponding
		// polarity data
		$sql = 'update value_info set one_star = ?, two_stars = ?, three_stars = ?, four_stars = ?, five_stars = ? where value = ?';
		$query = db()->prepare($sql);
		$query->execute(array($polarities[0], $polarities[1], $polarities[2], $polarities[3], $polarities[4], $value));
	}
}

//
// main
//

// Check input args
if ($argc != 2)
	die("Usage: php populate_value_ratings.php polaritydata.json\n");

if (!is_file($argv[1]))
	die("Could not open JSON input file '{$argv[1]}'.\n");

$rev_file = file_get_contents($argv[1]);

process_polarity_data($rev_file);

?>
