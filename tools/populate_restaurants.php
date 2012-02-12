<?php
/*
 * Populates restaurants from the raw place data. Requires the 'raw_places' table,
 * generously provided by Zach Stein <steinz@cs.washington.edu>.
 *
 * Run this in shell mode from the tools directory:
 *   php populate_restaurants.php
 *
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */
require_once '../src/web/includes/functions.php';  // Don't use common.php; it has session stuff
define('DEBUG', true);

// JSON attributes we want to extract
$json_attrs = array(n
	'Business Name' => 'name',
	'Latitude' => 'latitude',
	'Longitude' => 'longitude',
	'Hours' => 'hours',
	'Price Range' => 'price',
	'Accepts Credit Cards' => 'accepts_credit_cards',
	'Takes Reservations' => 'reservations'
);

// Takes the given JSON metadata ($metadata) and a mapping of JSON attributes to
// extract => database column names ($attrs), and returns an associative array
// that maps database column names to their values, as extracted from the given JSON.
function extract_restaurant($metadata, $attrs)
{
	$restaurant = array();
	
	// Map the JSON properties and their values to columns in the restaurant table.
	// property doesn't exist, it is given a value of NULL.
	foreach ($attrs as $json_key => $db_col)
	{
		$data = NULL;
		
		if (property_exists($metadata, $json_key))
		{
			// Special cases where the data type isn't just copied literally from the JSON blob
			switch ($json_key)
			{
				case 'Price Range':
					$data = strlen($metadata->$json_key);  // Prices are given in number of $'s
					break;
				case 'Latitude':
				case 'Longitude':
					$data = (float)($metadata->$json_key);
					break;
				case 'Accepts Credit Cards':
				case 'Takes Reservations':
					$data = ($metadata->$json_key == 'Yes');
					break;
				default:
					$data = $metadata->$json_key;
					break;
			}
		}
	
		$restaurant[$db_col] = $data;
	}
	
	return $restaurant;
}

//
// main
//

// Grab all raw place data
$sql = 'select id, metadata_json from raw_places';
$query = db()->prepare($sql);
$query->execute();

$data = $query->fetchAll(PDO::FETCH_ASSOC);
$restaurants = array();

foreach ($data as $row)
{
    // Decode JSON and grab the metadata we need
    $metadata = json_decode($row['metadata_json']);
	$restaurant = extract_restaurant($metadata, $json_attrs);
	
	// ID is not contained in the JSON, so we add it manually
	$restaurant['rid'] = $row['id'];
	
	$restaurants[] = $restaurant;
}

// Now insert the restaurant info into the database
foreach ($restaurants as $restaurant)
{
	db()->beginTransaction();
	
	// Build comma-separated column list. Surrounds each column name in backticks.
	$column_list = implode(', ', array_map(function($col) { return '`' . $col . '`'; }, array_keys($restaurant)));
	
	// Build comma-separated parameter list for binding. This prepends a ':'
	// to every column name (as per PDO-convention).
	$parameter_list = implode(', ', array_map(function($col) { return ':' . $col; }, array_keys($restaurant)));
	
	$sql = 'insert into restaurants(' . $column_list . ') values(' . $parameter_list . ')';
	$query = db()->prepare($sql);
	
	// Bind all the parameters from the column list
	foreach ($restaurant as $col => $val)
		$query->bindParam(':' . $col, $val);
	
	// Finalize the query
	$query->execute();
	db()->commit();
}

?>

