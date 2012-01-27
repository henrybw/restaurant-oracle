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
    
    <a href="#">Add new category preference</a><br />
    <div id="add_category" class="category">
      Name: <input name="category" type="text" /> <br />
      Rating: 
        <input type="radio" name="rating" value="1" /> 1
        <input type="radio" name="rating" value="2" /> 2
        <input type="radio" name="rating" value="3" /> 3
        <input type="radio" name="rating" value="4" /> 4
        <input type="radio" name="rating" value="5" /> 5
        <button type="button" onclick="add_category();">Add category</button>
    </div>
    <a href="#">Add new food preference</a>


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