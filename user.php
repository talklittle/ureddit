<?php

require('init.php');

$id = object_type_value_to_id($dbpdo, 'user',$_GET['id']);
if(count($id) == 0)
  send_user_to("/");

$id = $id[0]['id'];
$from = $_SESSION['user_id'];
$user = new user($dbpdo, $from);

if(!empty($_POST))
{

  $subj = $_POST['subj'];
  $msg = $_POST['msg'];
  if(strlen($subj)*strlen($msg) == 0)
    $error[] = "you must enter a subject and a message.";
  else
  {
    $to = $id;

    date_default_timezone_set('UTC');
    $datetime = date("Y-m-d H:i:s");
    $user->message($to, $subj, $msg);
    send_email(strtolower($user->value . "@ureddit.com"), $_GET['id'] . "@ureddit.com", $subj,process($msg));
     $success = true;
  }
}

$username = htmlspecialchars(substr($_GET['id'],0,32));
$validation = '/^([A-Z0-9_.-]){3,32}$/i';
if(!preg_match($validation,$username))
  send_user_to("/");

?>
<!DOCTYPE html>
<html>
<head>
<?php include('favicon.html'); ?>
<meta charset=UTF-8>
<title>University of Reddit</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=PREFIX ?>/jquery-1.4.2.min.js"></script>
</head>

<body>
<? require('header.php'); ?>
<div id="main">
  <?php
    if($_SESSION['logged_in'] == "true" && $_GET['id'] == $_SESSION['username'])
	echo "<div style=\"background-color: yellow; padding: 5px;\">Did you know that you are automatically given an @ureddit.com email address? Check <a href=\"http://ureddit.com/blog/?p=38\">here</a> and <a href=\"http://ureddit.com/blog/?p=49\">here</a> for the details. You can also set up a forwarding address <a href=\"" . PREFIX . "/settings\">here</a>.</div><br />";
  ?>
  <div class="pagetitle">
  User: <?=$username ?>
  </div>

  <div class="desc" style="margin-bottom: 30px;">
    <?php
    if(logged_in())
      {
	try
	  {
	    $ru = $user->get_attribute_value('reddit_username');
	    echo "You have already linked your UofR account to <a href=\"http://www.reddit.com/user/" . $ru . "\">your Reddit acount.</a><br />";
	  }
	catch (ObjectAttributeNotFoundException $e)
	  {
	    echo "You have not yet linked your UofR account to a Reddit account. <a href=\"" . PREFIX . "/confirm\">Link me to Reddit!</a><br />";
	  }
      }
    else
      {
	try
	  {
	    $viewed = new user($dbpdo, $id);
	    $vru = $viewed->get_attribute_value('reddit_username');
	    echo "<a href=\"http://www.reddit.com/message/compose/?to=" . $vru . "\">You can PM this user on Reddit.</a>";
	  }
	catch (ObjectAttributeNotFoundException $e)
	  {
	    echo "This user's account has not been linked to his or her Reddit account, if any.";
	  }
      }
    ?>
  </div>

  <?php
  if(logged_in())
  {
  ?>
  <div class="pagetitle">
  Message this user:
  </div>
  <div class="category">
  <div class="class" style="margin-bottom: 30px; padding-bottom: 0px;">
    <form method="post" action="<?=PREFIX ?>/user/<?=$username ?>">
    <?php
    if(isset($error) && count($error) > 0)
      { ?>
      <span style="color: red;"><?=$error[0] ?></span><br /><br />
      <?php }
    elseif(isset($success) && $success == true)
      { ?>
      your message has been sent!<br /><br />
      <?php } ?>
    <strong>Subject:</strong><br />
    <input type="text" name="subj" style="font-family: verdana; font-size: 1em; width: 600px;" value="<?=!empty($_POST) && isset($error) && count($error) > 0 ? htmlspecialchars(stripslashes($_POST['subj'])) : "" ?>"/><br /><br />

    <strong>Message:</strong><br />
    <textarea name="msg" style="font-family: verdana; font-size: 1em; width: 600px; height: 100px; padding: 3px;"><?=!empty($_POST) && isset($error) && count($error) > 0 ? htmlspecialchars(stripslashes($_POST['msg'])) : "" ?></textarea><br /><br />
    <input type="submit" value="Send PM" style="padding: 2px;" />
    </form><br />
  </div>
  </div>
  <?php } ?>

  <div class="pagetitle">
  Class schedule:
  </div>
  <?php
  display_schedule(new user($dbpdo, $id));
  ?>
</div>

<?php include('footer.php'); ?>

</body>
</html>