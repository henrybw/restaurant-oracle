<?php
/*
 * Populates categories from the raw place data. Requires the 'raw_places' table,
 * generously provided by Zach Stein <steinz@cs.washington.edu>.
 * 
 * Run this in shell mode from the tools directory:
 *   php populate_categories.php
 *
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */
require_once '../src/web/includes/functions.php';  // Don't use common.php; it has session stuff
define('DEBUG', true);

// Grab all raw place data
$sql = 'select metadata_json from raw_places';
$query = db()->prepare($sql);
$query->execute();

$data = $query->fetchAll(PDO::FETCH_ASSOC);
$all_categories = array();

foreach ($data as $row)
{
	// Decode JSON and grab category list
	$metadata = json_decode($row['metadata_json']);
	$categories = explode(', ', $metadata->{'Category'});
	
	foreach ($categories as $cat)
	{
		if ($cat != 'Restaurants' && !in_array($cat, $all_categories))
			$all_categories[] = $cat;
	}
}

// Now insert the categories into the database
foreach ($all_categories as $cat)
{
	db()->beginTransaction();

	$query = db()->prepare('insert into categories(name)' .
			   'values(:name)');
	$query->bindParam(':name', $cat);
	$query->execute();

	db()->commit();
}

?>
