<?php

require_once('init.php');

$course = new course($_POST['id']);

if(logged_in())
  {
    $user = new user($course->session('user_id']);
    $user->remove_association($user->id, $course->id, '%vote');
    switch($_POST['action'])
      {
      case upvote:
	$user->upvote($course->id);
	break;
      case downvote:
	$user->downvote($course->id);
	break;
      default:
	break;
      }
    votebox($course, $user);
  }
else
  {
    votebox($course,false);
  }

?>