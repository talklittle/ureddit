<?php

require_once('init.php');

$course = new course($dbpdo,$_POST['id']);

if(logged_in())
  {
    $user = new user($dbpdo, $course->session('user_id'));
    $user->remove_association($user->id, $course->id, 'upvote');
    $user->remove_association($user->id, $course->id, 'downvote');
    switch($_POST['action'])
      {
      case 'upvote':
	$user->upvote($course->id);
	break;
      case 'downvote':
	$user->downvote($course->id);
	break;
      default:
	break;
      }
    $user->get_votes();
    votebox($course, $user);
  }
else
  {
    votebox($course,false);
  }

?>