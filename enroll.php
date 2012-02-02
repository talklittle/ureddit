<?php

require_once('init.php');

$user_id = (int)$_SESSION['user_id'];
$class_id = (int)$_GET['id'];

if((int)$user_id != 0 && (int)$class_id != 0)
  {
    $user = new user($dbpdo, $user_id);
    $user->add_class($class_id);
    
    signup_button($class_id);
  }

?>