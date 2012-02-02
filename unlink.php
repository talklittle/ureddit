<?php

require_once('init.php');

if(logged_in())
  {
    $user = new user($dbpdo, $_SESSION['user_id']);
    $user->remove_attribute('reddit_username');
    send_user_to("/settings");
  }
else
  {
    send_user_to("/");
  }
?>