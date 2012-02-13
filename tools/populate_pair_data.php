<?php
/*
 * Populates our Restaurant Database with each restaurant's 
 * attribute-value pairs.for the given place data (in JSON).  
 *
 * Run this in shell mode from the tools directory:
 *   php populate_pair_data.php ../data/all.placeData
 *
 * @author Laure Thompson <laurejt@cs.washington.edu>
 */
require_once '../src/web/includes/functions.php';  // Don't use common.php; it has session stuff
define('DEBUG', true);

// Processes JSON object, which is expected to be in the following format:
//
// {"res1": {"att1": {"val1": <freq1>, "val2": <freq2> ... }, "att2" :... } "res2": ...}
//
// Given this, the function will populate the attribute_value_pairs
// datatable in our database.
function process_pair_data($rev_json)
{
	$rev_data = json_decode($rev_json, true);
	
	foreach ($rev_data as $restaurant_name => $attributes) 
	{
		$rid = get_rid($restaurant_name);
	
		if ($rid != NULL)
		{
			foreach ($attributes as $attr => $values) 
			{
				$aid = get_aid($attr);
				
				if ($aid != NULL) 
				{
					foreach ($values as $val => $freq) 
					{
						$vid = get_vid($val);
						
						if ($vid != NULL)
						{
							$sql = 'INSERT INTO attribute_value_pairs (rid, aid, vid, freq) VALUES(?, ?, ?, ?)';
							$query = db()->prepare($sql);
							$query->execute(array($rid, $aid, $vid, $freq));
						}
					}
				}
			}
		}
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

// Returns the attribute Id associated with a given attribute, or 
// NULL if the given attribute doesn't exist in the database.
function get_aid($attr) {
	$sql = 'select aid from attributes where attribute = ?';
	$query = db()->prepare($sql);
	$query->execute(array($attr));
	
	if ($query->rowCount() > 0)
	{
		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row['aid'];
	}
	else
	{
		return NULL;  // Attribute does not exist in our database
	}
	
}

// Returns the value Id associated with a given value, or 
// NULL if the given value doesn't exist in the database.
function get_vid($val) {
	$sql = 'select vid from value_info where value = ?';
	$query = db()->prepare($sql);
	$query->execute(array($val));
	
	if ($query->rowCount() > 0)
	{
		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row['vid'];
	}
	else
	{
		return NULL;  // Value does not exist in our database
	}
}


//
// main
//

// Check input args
if ($argc != 2)
	die("Usage: php populate_pair_data.php placedata.json\n");

if (!is_file($argv[1]))
	die("Could not open JSON input file '{$argv[1]}'.\n");

$rev_file = file_get_contents($argv[1]);

process_pair_data($rev_file);

?>
