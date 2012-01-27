<?php
/**
 * Defines global utility functions.
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'config.php';

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
 * Returns a handle to the current database connection, creating one if necessary.
 *
 * @return handle A resource handle to the database connection.
 */
function db()
{
	global $dbh, $db_name, $db_user, $db_pass;
	
	if (!$dbh)
	{
		set_exception_handler('exception_handler');  // To avoid repetitive try/catch blocks
		$dbh = new PDO("mysql:host=localhost;dbname=$db_name", $db_user, $db_pass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	return $dbh;
}

/**
 * Returns the currently logged-in user.
 *
 * @return integer The profile ID of the user that is currently logged
 *                 in user, or null if no user is logged in.
 */
function current_user()
{
	return (logged_in()) ? $_SESSION['profile_id'] : null;
}

/**
 * Returns the currently logged-in user.
 *
 * @param integer $profile_id The profile ID of the user that should be
 *                            set as the currently logged in user.
 */
function set_current_user($profile_id)
{
	$_SESSION['profile_id'] = $profile_id;
}

/**
 * Determines if the user is logged in or not.
 *
 * @return boolean Whether the user is logged in or not.
 */
function logged_in()
{
	return isset($_SESSION['profile_id']);
}

/**
 * Global exception handlers, usually used to handle boilerplate database
 * exceptions. If the given exception is a PDOException, the relevant error
 * message/code will be logged -- otherwise, the exception message itself
 * is logged. And, of course, execution is halted.
 *
 * @param PDOException $exception The exception that was thrown. 
 */
function exception_handler($exception)
{
	if ($exception instanceof PDOException)
	{
		db_error($exception, $exception->getFile(), $exception->getLine());
	}
	else
	{
		die_with_error('Exception thrown: ' . $exception->getMessage() .
		                   "\nStack Trace:\n" . $exception->getTraceAsString(),
		               $exception->getFile(), $exception->getLine());
	}
}

/**
 * Logs the given message to the Apache error log and halts execution.
 *
 * @param string $message The message to log.
 * @param string $file    The file that generated the error.
 * @param integer $line   The line of the file that generated the error.
 */
function die_with_error($message, $file, $line)
{
	// TODO: add some notification thing or use an error template
	if (DEBUG)
	{
		die("$file:$line -- $message");
	}
	else
	{
		error_log("$file:$line -- " . $message);
		die('An internal error occurred. We apologize for the inconvenience: the error '
		. 'has been logged, and we have been notified of the incident.');
	}
}

/**
 * Convenience function for database-related errors.
 * 
 * @param PDOException $exception The database exception representing the error.
 * @param string $file            The file that threw the exception.
 * @param integer $line           The line of the file that threw the exception.
 */
function db_error($exception)
{
	$error = $exception->errorInfo;
	die_with_error('Database error (' . $error[1] . '): ' . $error[2] . 
	                   "\nStack trace:\n" . $exception->getTraceAsString(),
	               $exception->getFile(),
	               $exception->getLine());
}

/**
 * Checks if the given argument list (usually a request superglobal, like $_GET
 * or $_POST) contains all of the given arguments.
 *
 * @param array $arg_list The argument list to look in.
 * @param array $args     The arguments that $arg_list must contain.
 * @return boolean        Whether the argument list contains all the specified args.
 */
function verify_args($arg_list, $args)
{
	foreach ($args as $arg)
	{
		if (!isset($arg_list[$arg]) || $arg_list[$arg] == '')
			return false;
	}

	return true;
}

/**
 * Sanitizes the given value by checking if it is in the given whitelist. If not,
 * this will assign the value to whatever $default is set to (which is null by
 * default).
 *
 * @param mixed $var       The value to sanitize.
 * @param array $whitelist An array of values that $var can be.
 * @param mixed $default   The value to assign to $var if it doesn't contain a
 *                         value in the whitelist. Defaults to null.
 * @return mixed           The sanitized variable.
 */
function whitelist($var, $whitelist, $default = null)
{
	return (array_search($var, $whitelist) !== false) ? $var : $default;
}

/**
 * Sanitizes the given input.
 *
 * @param string $str The string to sanitize.
 * @return string     The sanitized string.
 */
function sanitize($str)
{
	return htmlentities($str);
}

/**
 * Converts a string containing a date to a unified site-wide date format.
 *
 * @param string $date_str  The string that contains the date.
 * @param string $format    The date format to use. Uses DATE_FORMAT.
 * @return string           A formatted date string.
 */
function format_date($date_str, $format = DATE_FORMAT)
{
	$timestamp = strtotime($date_str);
	return ($timestamp) ? date(DATE_FORMAT, $timestamp) : '';
}

?>
