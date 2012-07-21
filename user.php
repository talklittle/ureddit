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
//$viewed->log_user_view();

$params['title'] .= ' : ' . $viewed->value;
require('header2.php');

?>
    <div id="user-header">
      <div class="content">
        <div class="username">
          Viewing user <?=$viewed->value ?>
        </div>
        <?php
        if(logged_in()&& $_GET['id'] == $dbpdo->session('username'))
	  echo "<div class=\"infobox\">Did you know that you are automatically given an @ureddit.com email address? Read the <a href=\"http://ureddit.com/blog/2012/02/23/details-on-ureddit-com-email/\">blog post</a> for details.</div><br />";
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
		echo "<p><a href=\"http://www.reddit.com/message/compose/?to=" . $vru . "\">You can PM this user on Reddit</a>.</p>";
	      }
	    catch (ObjectAttributeNotFoundException $e)
	      {
		echo "<p>This user's account has not been linked to his or her Reddit account, if any.</p>";
	      }
	    if(logged_in())
	      {
		?>
    <form method="post" action="<?=PREFIX ?>/user/<?=$username ?>">
		  <p>You can send this user a UReddit PM here (UReddit uses Markdown for formatting text, just like Reddit; <a href="http://old-wp.slekx.com/the-markdown-tutorial/" target="_blank">here</a> is an online tutorial if you are unfamiliar with the syntax):</p>
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
<?php require_once('footer2.php'); ?>
