<?php

require_once('init.php');

$error = array();
if(!empty($_POST))
  {
    if(!isset($_POST['age']) || ($_POST['age'] != "on" && $_POST['age'] != "checked"))
      $error[] = "you must be at least 13 years of age to use this website";

    $validation = array("username" => '/^([A-Z0-9_.-]){3,32}$/i', "password" => '/^(.*){6,}$/i', "email" => '/^([A-Z0-9._%+-])+@([A-Z0-9.-]+)\.[A-Z]{2,4}$/i');
    
    if(!preg_match($validation['username'],$_POST['username']))
      $error[] = "username: must be 4-32 characters; only alphanumeric characters and underscores, dots, and dashes, please";
    
    $username = $_POST['username'];
    $check_duplicates = $dbpdo->query("SELECT * FROM objects WHERE type = 'user' AND value = ?", array($username));
    if(count($check_duplicates) > 0)
      $error[] = "a user with that username already exists";
    
    if(!preg_match($validation['password'],$_POST['password']))
      $error[] = "password: must be at least 6 characters";
    
    if($_POST['password'] != $_POST['password2'])
      $error[] = "passwords must match";
    
    if(strlen($_POST['email']) > 0 && !preg_match($validation['email'],$_POST['email']))
      $error[] = "email was an invalid format";
    
    
    if(strlen($_POST['email']) > 0)
      {
	$email = $_POST['email'];
	$check_duplicates = $dbpdo->query("SELECT * FROM object_attributes WHERE type = 'email_address' AND value = ?", array($email));
	if(count($check_duplicates) > 0)
	  $error[] = "a user with that email address already exists";
      }
    
    if(count($error) == 0)
      {
	$password = $_POST['password'];
	$email = $_POST['email'];
	strlen($email) > 0 ? $email = $_POST['email'] : "";
	$datetime = date("Y-m-d H:i:s");

	$user = new user($dbpdo);

	$user->define('user',$username,0);
	$user->save();

	$user->define_attribute('email',$email,0);
	$user->define_attribute('password_crypt',$user->crypt_password($password,$user->id), 0);
	$user->save();

	$fUsername = "$username@ureddit.com";
	$fName = $username;
	$quotamb = 1;
	$qm = 1024000;
	$quota = $quotamb * $qm;
	$maildir = strtolower($fUsername) . "/";
	$local_part = $username;
	$fDomain = "ureddit.com";
	$sqlActive = 1;
	$emailpassword = pacrypt(escape_string($_POST['password']));

	

	if(config::postfix)
	  {
	    $now = $dbpdo->timestamp();
	    $dbpdo->query("INSERT INTO pf_mailbox (username, password, name, maildir, local_part, quota, domain, created, modified, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
			  array(
				$fUsername,
				$emailpassword,
				$fName,
				first_letter_subdir($maildir),
				$local_part,
				$quota,
				$fDomain,
				$now,
				$now,
				$sqlActive
				));
	
	    $dbpdo->query("INSERT INTO pf_alias (address, goto, domain, created, modified, active) VALUES (?, ?, ?, ?, ?, ?)", 
			  array(
				$fUsername,
				$fUsername,
				$fDomain,
				$now,
				$now,
				$sqlActive
				));
	    
	    
	    $fHeaders = "Welcome to your new account! Please note that this account has a quota of $quotamb MB. It is meant for communication and not for sending large attachments.\n\nUniversity of Reddit Admins";
	    @send_email("admin@ureddit.com",$fUsername, "Welcome to University of Reddit!", $fHeaders);
	  }
	
	login($user);
	send_user_to("/user/" . $username);
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
  <?php include('includes.php'); ?>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('header.php');
  require_once('social.php');

  ?>
  <div id="main" role="main">
    <div id="register">
      <div class="content">
        <h1>Register</h1>
        <form method="post" action="<?=PREFIX ?>/register">
          username:<br />
          <input type="text" name="username" id="username" size="25" value="<?=post('username'); ?>" /><br /><br />

          password:<br />
          <input type="password" name="password" id="password" size="25" /><br /><br />

          confirm password:<br />
          <input type="password" name="password2" id="password2" size="25" /><br /><br />

          email (optional):<br />
          <input type="email" name="email" id="email" value="<?=post('email'); ?>" size="25" /><br /><br />

          <label><input type="checkbox" name="age" id="age"> I am at least 13 years of age.</label><br /><br />

          <input type="submit" />
        </form><br />
<?php
    if(!empty($_POST) && count($error) > 0)
      foreach($error as $err)
	echo '<span style="color: red;">' . $err . '</span><br />';
?>
      </div>
    </div>
    <div id="whyregister">
      <div class="content">
        <p>
    <strong>Already have an account?</strong> <a href="<?=PREFIX ?>/login"><br>Log in</a>!
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
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
