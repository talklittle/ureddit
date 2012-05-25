<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$user = new user($dbpdo, $_SESSION['user_id']);
$class = new course($dbpdo, $_GET['id']);
$class->get_owner();
$class->get_teachers();
$class->get_categories();
$class->get_attributes();

$teacher_check = @mysql_fetch_assoc(mysql_query("SELECT COUNT(*) FROM `classes` WHERE `id`='$class_id' AND `teacher_id`='$user_id'"));
if($class->owner != $user->id)
  send_user_to("/teachers");

$id = object_type_value_to_id($dbpdo, 'user',$_GET['username']);
if(count($id) != 0)
  {
    $class->remove_teacher((int)$id[0]['id']);
    $user->message((int)$id[0]['id'],"Your teacher status has been revoked.","User " . $user->value . " has removed you as a teacher for class \"[" . $class->value . "](http://ureddit.com/c" . $class->id . ")\".");
  }

send_user_to("/class/" . $class->id . "/teachers");

?>