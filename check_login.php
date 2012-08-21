<?php

session_start();

if(!logged_in() && cookie_exists())
  {
    $user = $dbpdo->query("SELECT `object_id` FROM `sessions` WHERE `session_id` = ?", array($_COOKIE[COOKIE_SESSID]));
    if($user->is_banned())
      {
	header("Location: http://sadtrombone.com/");
	die();
      }
    if(count($user) > 0)
      {
	try
	  {
	    $user = new user($dbpdo, $user[0]['object_id']);	    
	    login($user);
	  }
	catch (ObjectNotFoundException $e)
	  {
	  }
	catch (ObjectAttributeNotFoundException $e)
	  {
	  }
      }
  }

?>