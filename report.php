<?php

require_once('init.php');

if(logged_in())
  {
    $user = new user($dbpdo, $_SESSION['user_id']);
    $user->report_class($_POST['class']);
  }

?><strong>reported</strong>