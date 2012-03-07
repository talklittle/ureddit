<?php
require_once('init.php');

$id = object_type_value_to_id($dbpdo, 'user',$_GET['id']);
if(count($id) == 0)
  send_user_to("/");

$id = $id[0]['id'];
$from = $dbpdo->session('user_id');
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
    $success = true;
  }
}

$username = htmlspecialchars(substr($_GET['id'],0,32));
$validation = '/^([A-Z0-9_.-]){3,32}$/i';
if(!preg_match($validation,$username))
  send_user_to("/");

$viewed = new user($dbpdo, $id);
$viewed->log_user_view();

?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>University of Reddit : <?=$viewed->value ?></title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="<?=PREFIX ?>/css/style.css">

  <script src="<?=PREFIX ?>/js/libs/modernizr-2.5.2.min.js"></script>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('header.php');
  require_once('social.php');
  ?>
  <div id="main" role="main">
    <div id="user-header">
      <div class="content">
        <div class="username">
          Viewing user <?=$viewed->value ?>
        </div>
        <?php
        if(logged_in()&& $_GET['id'] == $dbpdo->session('username'))
	  echo "<div class=\"infobox\">Did you know that you are automatically given an @ureddit.com email address? Access using <a href=\"http://ureddit.com/webmail\">webmail</a> <em>or</em> any email client set for incoming port 993 for IMAP+SSL or port 110 for POP3, with outgoing port 465 for STMP-SSL. You can also set up a forwarding address <a href=\"" . PREFIX . "/settings\">here</a>.</div><br />";
        ?>


        <?php
        if(logged_in() && $_GET['id'] == $dbpdo->session('username'))
	  {
	    try
	      {
		$ru = $user->get_attribute_value('reddit_username');
		echo "You have already linked your UofR account to <a href=\"http://www.reddit.com/user/" . $ru . "\">your Reddit account.</a> (<a href=\"" . PREFIX . "/settings\">Unlink</a>?)";
	      }
	    catch (ObjectAttributeNotFoundException $e)
	      {
		echo "You have not yet linked your UofR account to a Reddit account. <a href=\"" . PREFIX . "/confirm\">Link me to Reddit!</a>";
	      }
	  }
	else
	  {
	    try
	      {
		$vru = $viewed->get_attribute_value('reddit_username');
		echo "<p><a href=\"http://www.reddit.com/message/compose/?to=" . $vru . "\">You can PM this user on Reddit</a> - or via UReddit:</p>";
	      }
	    catch (ObjectAttributeNotFoundException $e)
	      {
		echo "<p>This user's account has not been linked to his or her Reddit account, if any. You can send this user a UReddit PM here:</p>";
	      }
	    if(logged_in())
	      {
		?>
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
		<?php
	      }
	  }
        ?>
      </div>
    </div>
    <div id="user-schedule">
      <div class="content">
          <h2>Class schedule</h2>
	  <?php
	  display_schedule($viewed);
          ?>
      </div>
    </div>
    <div id="user">
      <div class="content">
        <h2>Activity Feed</h2>
        <ul>
          <?php
            $feed = get_feed($viewed);
            echo implode("<br>", $feed);
          ?>
        </ul>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
