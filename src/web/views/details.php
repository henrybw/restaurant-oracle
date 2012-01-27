<?php
/**
 * View for the restaurant details page.
 * 
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

// File should never be requested directly
if (basename(getcwd()) == basename(dirname(__FILE__)))
{
	include('404.shtml');
	die();
}

//-----------------------------------------------------------------------------
// Functions
//-----------------------------------------------------------------------------

/**
 * Convenience function for displaying booleans
 * 
 * @param boolean $flag The boolean to display.
 * @return string The string representation of the boolean.
 */
function format_bool($flag)
{
	return ($flag) ? 'Yes' : 'No';
}

/**
 * TODO: document
 * TODO: nicer formatting
 */
function format_metadata_attribute($category, $value, $bool_val = false)
{
	// Value is null when it is not applicable, in which case we should
	// output nothing.
	if ($value)
	{
		$value = ($bool_val) ? format_bool($value) : sanitize($value);
		return sanitize($category) . ': ' . $value . '<br />';
	}
	else
	{
		return '';
	}
}

/**
 * Generates the page body for this page.
 *
 * @param array $data Data for the page.
 */
function page_body($data = null)
{
	if ($data)
	{
?>
			<h2><?= sanitize($data['name']) ?></h2>
			<?= format_metadata_attribute("Address", $data['location']) ?>
			<?= format_metadata_attribute("Hours", $data['hours']) ?><!-- TODO: display an "is open?" thing -->
			<?= format_metadata_attribute("Phone", $data['phone']) ?>
			<?= format_metadata_attribute("Parking", $data['parking']) ?>
			<?= format_metadata_attribute("Accepts Credit Cards", $data['accepts_credit_cards'], true) ?>
			<?= format_metadata_attribute("Take Out", $data['take_out'], true) ?>
			<?= format_metadata_attribute("Delivery", $data['delivery'], true) ?>
			<?= format_metadata_attribute("Alcohol", $data['alcohol'], true) ?>
			<?= format_metadata_attribute("Takes Reservations", $data['reservations'], true) ?>
			<?= format_metadata_attribute("Ideal for Groups", $data['ideal_for_groups'], true) ?>
<?php
	}
	else
	{
?>
			<p>The requested restaurant was not found.</p>
<?php
	}
}
?>
