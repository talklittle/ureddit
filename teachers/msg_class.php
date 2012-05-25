<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$class = new course($dbpdo, $_GET['id']);
$user = new user($dbpdo, $_SESSION['user_id']);

$class->get_teachers();
$class->get_owner();

if($class->owner != $user->id && !in_array($user->id,$class->teachers))
  send_user_to("/teachers/");

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
    tweet($user->config, $tweet);
  }
}

?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>University of Reddit</title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <?php include('../includes.php'); ?>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('../header.php');
  require_once('../social.php');

  ?>
  <div id="main" role="main">
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
    </div>
    <div id="teach-side">
      <div class="content" style="border-bottom: 3px solid #232323">
        <h2>Note:</h2>

        Please click the button only once. It may take some time to deliver a mass message to several hundred people.

    <?php include('tools.php'); ?>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('../footer.php'); ?>
</body>
</html>
