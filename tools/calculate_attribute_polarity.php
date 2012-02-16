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
$sql = 'SELECT rid, aid, frequency FROM restaurant_attributes WHERE polarity = 0';
$query = db()->prepare($sql);
$query->execute();

$count = 1;

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
	print 'Start processing row: ' . $count . "\n";
	
	$rid = $row ['rid'];
	$aid = $row['aid'];
	$attr_freq = $row['frequency'];
	$sql = 'SELECT vid, frequency FROM attribute_value_pairs WHERE rid = ? AND aid = ?';
	$query2 = db()->prepare($sql);
	$query2->execute(array($rid, $aid));
	
	
	print $rid . "\n";
	print $aid . "\n";
	
	while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
		$vid = $row2['vid'];
		$val_freq = $row2['frequency'];
		
		$sql = 'SELECT polarity FROM value_info WHERE vid = ?';
		$query3 = db()->prepare($sql);
		$query3->execute(array($vid));
		
		$data = $query3->fetch(PDO::FETCH_ASSOC);
		
		$sql = 'UPDATE restaurant_attributes SET polarity = (polarity + '
			   . '(? / ? * ?)) WHERE rid = ? AND aid = ?';
			   
		print (double)($val_freq / $attr_freq + $data['polarity']);
		
		$query3 = db()->prepare($sql);
		$query3->execute(array($val_freq, $attr_freq, $data['polarity'], $rid, $aid));
	}
	
	print 'End processing row: ' . $count . "\n";
	$count++;
	
	if ($count % 500 == 0) {
		sleep(5);
	}
}
?>