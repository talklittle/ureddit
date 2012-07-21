<?php

require_once('init.php');
$loginmsg = "";

$error = array();
if(!empty($_POST))
  {
    $user = new user($dbpdo);
    if($user->verify_credentials($_POST['username'], $_POST['password']))
      {
	try
	  {
	    $user->get_attribute_value('banned');
	  }
	catch (ObjectAttributeNotFoundException $e)
	  {
	    $error[] = 'that account has been banned';
	  }

	login($user);	
	@setcookie("ureddit_sessid", session_id(),time()+(60*60*24*365*5));
	$dbpdo->query("INSERT INTO `sessions` (`object_id`,`session_id`) VALUES (?, ?)",
		      array(
			    $user->id,
			    session_id()
			    ));

	$dbpdo->query("INSERT INTO `logins` (`object_id`,`datetime`) VALUES (?, ?)",
		      array(
			    $user->id,
			    $user->timestamp()
			    ));
      }
    else
      {
	$error[] = "the username or password you entered is incorrect";
      }
  }

if(logged_in())
  send_user_to("/user/" . $_SESSION['username'],"ureddit.com");

$params['title'] .= ' : Log In';
require('header2.php');

?>
    <div id="login">
      <div class="content">
        <h1>Log In</h1>
        <form method="post" action="<?=PREFIX ?>/login">
        <p>
        Username:<br />
        <input type="text" name="username" id="username" /><br /><br />

        Password:<br />
        <input type="password" name="password" id="password" /><br /><br />

        <input type="submit" />
        </form><br />
<?php
    if(!empty($_POST) && count($error) > 0)
      foreach($error as $err)
	echo '<span style="color: red;">' . $err . '</span><br />';
?>
        <p>
        <strong>Forgot your password?</strong> <a href="<?=PREFIX ?>/recover_password"><br>Reset my password</a></p>
        </p>
      </div>
    </div>
    <div id="whyregister">
      <div class="content">
        <p>
    <strong>Don't have an account?</strong> <a href="<?=PREFIX ?>/register"><br>Register</a>!
        </p>

        <p>
        <strong>Why should you register?</strong><br>
By registering an account, you will be able to add classes to your personal schedule and thereby use your account as an organizational tool. Furthermore, teachers often send out mass messages to users that have added their class with class updates, new material, and so on, so you'll be automatically kept up to date.
        </p>
        <p>
 You'll also get an @ureddit.com email address you can check from webmail, your mail client, Gmail account, or smartphone in order to always be up to date!
        </p>
      </div>
    </div>
<?php require_once('footer2.php'); ?>
