<?php

session_start();

if(!logged_in() && cookie_exists())
  {
    $user = $dbpdo->query("SELECT `object_id` FROM `sessions` WHERE `session_id` = ?", array($_COOKIE[COOKIE_SESSID]));
    if(count($user) > 0)
      {
	try
	  {
	    $user = new user($dbpdo, $user[0]['user_id']);
	    if($user->get_attribute_value('banned') == 'true')
	      die('banned');
	    
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