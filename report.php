<?php

require_once('init.php');

if(logged_in())
  {
    $user = new user($dbpdo, $_SESSION['user_id']);
    $user->report_class($_POST['class']);
    $class = new course($dbpdo, $_POST['class']);
    $user->message(1770, '[UReddit] Please review class "' . $class->value . '"', $user->value . ' reported ' . $class->value . ' at ' . $user->timestamp() . '; please [address it](http://ureddit.com/admin).');
  }

?><strong>reported</strong>