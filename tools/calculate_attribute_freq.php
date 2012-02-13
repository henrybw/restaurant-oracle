<?php
/*
 * Populates frequency for each restaurant-attribute pair from the
 * attribute_value_pairs data.
 * 
 * Run this in shell mode from the tools directory:
 *   php calculate_attribute_freq.php
 *
 * @author Laure Thompson <laurejt@cs.washington.edu>
 */
require_once '../src/web/includes/functions.php';  // Don't use common.php; it has session stuff
define('DEBUG', true);

// Grab the sums corresponding to each attribute-restaraunt pair
$sql = 'SELECT rid, aid, SUM(frequency) FROM attribute_value_pairs GROUP BY rid, aid';
$query = db()->prepare($sql);
$query->execute();

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
	$sql = 'INSERT INTO restaurant_attributes (rid, aid, frequency) VALUES(?, ?, ?)';
	$query2 = db()->prepare($sql);
	$query2->execute(array($row['rid'], $row['aid'], $row['SUM(frequency)']));
}
?>