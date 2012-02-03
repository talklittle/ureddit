<?php

require_once('init.php');

$user_id = $dbpdo->session('user_id');
$class_id = (int)$_GET['id'];

if($user_id && $class_id != 0)
  {
    $user = new user($dbpdo, $user_id);
    $user->add_class($class_id);
    
    signup_button($dbpdo,$class_id);
  }

?>