<?php

require_once('init.php');

$error = array();
if(!empty($_POST))
  {

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
	//$username = mysql_real_escape_string($_POST['username']);
	$password = md5(md5($_POST['password']) . "uofr!1336");
	$email = $_POST['email'];
	strlen($email) > 0 ? $email = $_POST['email'] : "";
	$datetime = date("Y-m-d H:i:s");

	$user = new user($dbpdo);

	$user->define('user',$username,0);
	$user->define_attribute('password_hash',$password,0);
	$user->define_attribute('email',$email,0);
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

	$dbpdo->query("INSERT INTO pf_mailbox (username, password, name, maildir, local_part, quota, domain, created, modified, active) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?",
	  array(
		$fUsername,
		$emailpassword,
		$fName,
		$maildir,
		$local_part,
		$quota,
		$fDomain,
		$sqlActive
		));
	
	$dbpdo->query("INSERT INTO pf_alias (address, goto, domain, created, modified, active) VALUES (?, ?, ?, NOW(), NOW(), ?)", 
		      array(
			    $fUsername,
			    $fUsername,
			    $fDomain,
			    NOW(),
			    NOW(),
			    $sqlActive
			    ));
	
	
	$fHeaders = "Welcome to your new account! Please note that this account has a quota of $quotamb MB. It is meant for communication and not for sending large attachments.\n\nUniversity of Reddit Admins";
	@send_email("admin@ureddit.com",$fUsername, "Welcome to University of Reddit!", $fHeaders);

	
	login($user);
	send_user_to("/user/" . $username);
      }
  }
?>
<!DOCTYPE html>
<html>
<head>
<?php include('favicon.html'); ?>
<meta charset=UTF-8>
<title>University of Reddit - register</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<? require('header.php'); ?>
<div id="main">
  <h2>register</h2>

  <div style="color: red;">
   <?
     foreach($error as $err)
     echo "- $err<br />\n";
   ?><br />
  </div>
  <form method="post" action="<?=PREFIX ?>/register">
username:<br />
<input type="text" name="username" id="username" size="25" value="<?=post('username'); ?>" /><br /><br />

 password:<br />
<input type="password" name="password" id="password" size="25" /><br /><br />

  confirm password:<br />
<input type="password" name="password2" id="password2" size="25" /><br /><br />

  email (optional):<br />
<input type="email" name="email" id="email" value="<?=post('email'); ?>" size="25" /><br /><br />

<input type="submit" />
</form>
</div>

<?php include('footer.php'); ?>

</body>
</html>
