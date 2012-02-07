<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$class = new course($dbpdo, $_GET['id']);
$user = new user($dbpdo, $_SESSION['user_id']);

$class->get_teachers();
$class->get_owner();

if($class->owner != $user->id || !in_array($user->id,$class->teachers))
  send_user_to("/teachers/index.php");

$success = false;
$error = array();
if(!empty($_POST))
{
  $subj = $_POST['subj'];
  $msg = $_POST['msg'];

  if(strlen($subj)*strlen($msg) == 0)
    $error[] = "you must enter a subject and a message.";
  else
  {
    $from = $user->id;
    date_default_timezone_set('UTC');
    $datetime = date("Y-m-d H:i:s");
    
    $class->mass_message($subj, $msg, $user->id);
    foreach($class->roster as $child_id)
      {
	$student = new user($dbpdo, $child_id);
	send_email(strtolower($user->value . "@ureddit.com"), $student->value . "@ureddit.com", $subj,process($msg));
      }
    $success = true;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<?php include('../favicon.html'); ?>
<meta charset=UTF-8>
<title>University of Reddit</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<? require('../header.php'); ?>
<div id="main">
  <div class="pagetitle">
    PM class: <?=$class->value ?>
  </div>

<div style="color: red;">
<?php
if(count($error) != 0)
  foreach($error as $err)
    echo "$err<br />\n";
elseif($success == true)
  echo "your mass message was delivered<br><br>";
?>
</div><br />

<form method="post" action="<?=PREFIX ?>/teachers/msg_class.php?id=<?=$_GET['id'] ?>">
Subject:<br />
<input type="text" name="subj" class="teach" value="<?=!empty($_POST) && isset($error) && count($error) > 0 ? htmlspecialchars(stripslashes($_POST['subj'])) : "" ?>" />
<br /><br />

Message:<br />
<textarea name="msg" class="teach"><?=!empty($_POST) && isset($error) && count($error) > 0 ? htmlspecialchars(stripslashes($_POST['msg'])) : "" ?></textarea>
<br /><br />

<input type="submit" value="Send PMs" style="padding: 3px;" />
</form>

</div>

</body>
</html>
