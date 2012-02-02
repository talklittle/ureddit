<?php

session_start();

if(!logged_in() && cookie_exists())
  {
    $user = $dbpdo->query("SELECT `user_id` FROM `sessions` WHERE `session_id` = ?", array($_COOKIE[COOKIE_SESSID]));

    try
      {
	$user = new user($dbpdo, $user[0]['user_id']);
	$user->get_attributes();
	if(isset($user->attributes['banned']))
	  die('banned');
	
	login($user);
      }
    catch (UserNotFoundException $e)
      {
      }
  }

?>