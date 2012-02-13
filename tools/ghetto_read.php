<?php

//
// main
//

$start = 249344 + 248142 + 249018 + 245953 + 249563 + 247597 + 249622 +
		 249857 + 242598 + 244927 + 249207 + 248465 + 249964 + 248827 +
		 249562 + 242221 + 249651 + 249826 + 246044 + 249902 + 248933 +
		 247625 + 249010 + 248943 + 247086 + 243879 + 249537 + 249801 +
		 249399 + 249772 + 244646 + 249896 + 249713 + 245649 + 249954 +
		 242916 + 249763 + 249951 + 248722 + 249570 + 247437 + 249585 +
		 248956 + 245391 + 246276 + 249939 + 249936 + 249797 + 46258;
		 
$chunk_size = 250000;

// Check input args
if ($argc != 2)
	die("Usage: php ghetto_read.php placedata.json\n");

if (!is_file($argv[1]))
	die("Could not open JSON input file '{$argv[1]}'.\n");

// Parse the JSON file line-by-line, since each line corresponds to a set of
// reviews for one restaurant.
$rev_file = fopen($argv[1], 'r');

fseek($rev_file, $start);

$stuff = fread($rev_file, $chunk_size);

echo $stuff;

fclose($rev_file);
?>
