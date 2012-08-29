<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$class = new course($dbpdo, $_GET['id']);
$user = new user($dbpdo, $_SESSION['user_id']);

$class->get_teachers();
$class->get_owner();

if($class->owner != $user->id && !in_array($user->id,$class->teachers))
  send_user_to("/class/" . $class->id . "/" . $class->seo_string($class->value));

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

    $tweet = "(" . date("U") . ") " . $user->value . " PMed the students in " . $class->value;
    if(strlen($tweet) > 115)
      $tweet = substr($tweet, 0, 115) . "...";
    $tweet .= " http://ureddit.com/c" . $class->id;
    if(strlen($tweet) < 126)
      $tweet .= " with subject line \"$subj\"";
    if(strlen($tweet) > 140)
      $tweet = substr($tweet, 0, 136) . "...\"";
      //$tweet .= ": \"$subj\"";
    //tweet($user->config, $tweet);
  }
}

$params['title'] .= ' : Mass Message';
require('../header2.php');

?>
    <div id="teach">
      <div class="content">
        <h1>Mass Message Class</h1>
<div style="color: red;">
<?php
if(count($error) != 0)
  foreach($error as $err)
    echo "$err<br />\n";
elseif($success == true)
  echo "your mass message was delivered<br><br>";
?>
</div><br />

<form method="post" action="<?=PREFIX ?>/teachers/msg_class.php?id=<?=$_GET['id'] ?>" id="form">
Subject:<br />
<input type="text" name="subj" class="teach" value="<?=!empty($_POST) && isset($error) && count($error) > 0 ? htmlspecialchars(stripslashes($_POST['subj'])) : "" ?>" />
<br /><br />

Message:<br />
<textarea name="msg" class="teach"><?=!empty($_POST) && isset($error) && count($error) > 0 ? htmlspecialchars(stripslashes($_POST['msg'])) : "" ?></textarea>
<br /><br />

<input type="submit" value="Send PMs" style="padding: 3px;" onclick="$(this).attr('disabled','disabled'); $('#form').submit();"/>
</form>
      </div>
    </div>
    <div id="teach-side">
      <div class="content" style="border-bottom: 3px solid #232323">
        <h2>Note:</h2>

        Please click the button only once. It may take some time to deliver a mass message to several hundred people.

    <?php include('tools.php'); ?>
      </div>
    </div>
  <?php require_once('../footer2.php'); ?>