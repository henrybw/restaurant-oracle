<?
session_start();

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

function page_body($data = null)
{

  if (isset($data)) {
?>
    <p>Preferences: </p>
    <table border=1>
       <tr><th>Food type</th><th>Rating</th></tr>
<?php    

    print_r($data);


    foreach ($data as $pref) {
?>
       <tr><td><?= $pref['category'] ?></td><td><?= $pref['rating'] ?></td></tr>
<?php
    }

?>
    </table>
<?php


  } else {
?>
    <p>No preferences set!</p>
<?php
  }


}