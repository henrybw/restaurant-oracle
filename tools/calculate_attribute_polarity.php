<?php
/*
 * Populates frequency for each restaurant-attribute pair from the
 * attribute_value_pairs data.
 * 
 * Run this in shell mode from the tools directory:
 *   php calculate_attribute_frequency.php
 *
 * @author Laure Thompson <laurejt@cs.washington.edu>
 */
require_once '../src/web/includes/functions.php';  // Don't use common.php; it has session stuff
define('DEBUG', true);

// Grab the sums corresponding to each attribute-restaraunt pair
$sql = 'SELECT rid, aid, SUM(frequency) FROM attribute_value_pairs GROUP BY rid, aid';
$query = db()->prepare($sql);
$query->execute();
$data = $query->fetchAll(PDO::FETCH_ASSOC);

echo $data;
?>