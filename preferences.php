<?php

require('init.php');

if(!logged_in())
  send_user_to("/login");

try
  {
    $user = new user($dbpdo, $_SESSION['user_id']);
    $user_signature = $user->get_attribute_value('signature');
  }
catch (ObjectAttributeNotFoundException $e)
  {
    $user_signature = encrypt($user->value, config::private_key);
    $user->define_attribute('signature', $user_signature, 0);
    $user->save();
  }
catch (UserNotFoundException $e)
  {
    send_user_to("/");
  }

$error = array();
if(!empty($_POST))
  {
    if(!$user->verify_credentials($user->value, $_POST['password']))
      $error[] = "The password you entered was incorrect.";
    else
      {
	$email = $_POST['forward_addr'];
	if(strlen($email) == 0)
	  $email = $_SESSION['username'] . "@ureddit.com";
	else
	  if(!preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $_POST['forward_addr']))
	    $error[] = "You did not seem to enter a valid email address. If you did, in fact, then please contact us so that we can update our validator.";
	
	if(strlen($_POST['newpass']) != 0)
	  {
	    if(!preg_match('/^(.*){6,}$/', $_POST['newpass']))
	      $error[] = "Your new password must be at least 6 characters.";
	    if($_POST['newpass'] != $_POST['newpass2'])
	      $error[] = "Your did not enter the same new password twice.";
	  }
      }

    if(count($error) == 0)
      {
	if(strlen($_POST['newpass']) != 0)
	  {
	    //$newpass = $user->hash_password($_POST['newpass']);
	    $newcrypt = $user->crypt_password($_POST['newpass'], $user->id);
	    //$user->define_attribute('password_hash', $newpass, 0);
	    $user->define_attribute('password_crypt', $newcrypt, 0);

	    $newemailpass = pacrypt(escape_string($_POST['newpass']));

	    $dbpdo->query("UPDATE pf_mailbox SET password = ? WHERE username = ?",
			  array(
				$newemailpass,
				$user->value . '@ureddit.com'
				));

	  }
	if(strlen($email) != 0)
	  {
	    $exp = explode("@",$email);

	    $dbpdo->query("UPDATE pf_alias SET goto = ?, domain = ? WHERE address = ?",
			  array(
				$email,
				$exp[1],
				$user->value . '@ureddit.com'
				));

	  }
      }
  }

$mailboxes = $dbpdo->query("SELECT goto FROM pf_alias WHERE address = ?", array($_SESSION['username'] . '@ureddit.com'));
$forwarded_email = $mailboxes[0]['goto'];

$params['title'] .= ' : Settings';
require('header2.php');

?>
    <div id="settings">
      <div class="content">
      <h1>Settings</h1>
      <?php
      if(!empty($_POST) && count($error) > 0)
	{
	  echo '<span style="color: red;">';
	  foreach($error as $err)
	    echo $err . "<br />\n";
	  echo '</span><br /><br />';
	}
      ?>

      <p>
      <form method="post" action="<?=PREFIX ?>/settings">
      New password (leave blank if you do not want to reset your password):<br />
      <input type="password" name="newpass" /><br /><br />

      Confirm new password:<br />
      <input type="password" name="newpass2" /><br /><br />

      Address to which to forward your @ureddit.com email (leave blank to disable forwarding):<br />
      <input type="text" name="forward_addr" value="<?=$forwarded_email ?>" /><br /><br />

      Current password (required to make any changes):<br />
      <input type="password" name="password" ?><br /><br />

      <input type="submit" value="Save changes" /><br /><br />
      </form>
      </p>

      <p>
      Your UReddit signature:<br />
      <?php
        if(isset($_SERVER['HTTPS']) && strlen($_SERVER['HTTPS']) > 0)
	  {
	    echo $user_signature;
	  }
	else
	  {
	    ?><em>You must be <a href="https://ureddit.com<?=PREFIX ?>/settings">using HTTPS</a> to view this information.</em><?php
	  }
      ?></p>

      <?php
      try
      {
	$ru = $user->get_attribute_value('reddit_username');
	?>
	<span style="font-size: 0.75em;"><a href="unlink.php">Click here to unlink your Reddit account</a></span><br /><br />
	<?php
      }
    catch(ObjectAttributeNotFoundException $e)
      {
      }

    if(!empty($_POST) && count($error) == 0)
      {
	echo '<strong>Your changes have been saved and have gone into effect.</strong>';
      }
    ?>
    </p>
    </div>
    </div>
<?php require_once('footer2.php'); ?>
