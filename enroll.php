<?php

require_once('init.php');

$user_id = $dbpdo->session('user_id');
$class_id = (int)$_GET['id'];

if($user_id && $class_id != 0)
  {
    $user = new user($dbpdo, $user_id);
    $user->add_class($class_id);
    
    if(date("m") == "04" && date("j") == "1")
      die("<em>We're sorry, you must be a UReddit Gold &reg; member in order to do that. Please <a href=\"http://www.reddit.com/message/compose?to=%2Fr%2FUniversityofReddit&subject=UReddit+Gold\">contact us</a> to request membership at the rate of \$5/day (or, if more convenient, one goat per year)</em>.");
    signup_button($user,$class_id);
  }

?>