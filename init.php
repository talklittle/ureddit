<?php

require('markdown.php');
require('twitter.class.php');
require('php/base.class.php');
require('php/object.class.php');
require('php/catalog.class.php');
require('php/category.class.php');
require('php/course.class.php');
require('php/user.class.php');
require('php/lecture.class.php');
require('php/link.class.php');
require('php/api.class.php');

require('dbconnect.php');
require('supporting_functions.php');
require('check_login.php');

$params = array();
$params['title'] = "University of Reddit";

if(logged_in())
  {
    try
      {
	$user = new user($dbpdo, $dbpdo->session('user_id'));
	if($user === NULL || $user->is_banned())
	  {
	    header("Location: http://sadtrombone.com/");
	    die();
	  }
      }
    catch(ObjectNotFoundException $e)
      {
	die();
      }
  }

?>