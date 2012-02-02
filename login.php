 <?php

require_once('init.php');
$loginmsg = "";

if(!empty($_POST))
  {
    $username = $_POST['username'];
    if(!preg_match('/^([A-Z0-9_.-]*)$/i', $username))
      die('The username you submitted is not a valid username. Are you trying to use your email address to log in? <a href="login.php">Click here to try again.</a><br /><br />(Yes, we are aware that this method of handling this error is cumbersome; we will fix it sometime in the near future.)');
    $hash = md5(md5($_POST['password']) . "uofr!1336");

    $users = $dbpdo->query("SELECT objects.id FROM objects INNER JOIN object_attributes ON objects.value = ? AND object_attributes.type = 'password_hash' AND object_attributes.object_id = objects.id AND object_attributes.value = ?",
			   array(
				 $username,
				 $hash
				 ));

    if(count($users) != 0)
      {
	$user = new user($users[0]['id']);
	try
	  {
	    $user->get_attribute_value('banned');
	    die("<h1>you have been banned</h1>");
	  }
	catch (ObjectAttributeNotFoundException $e)
	  {
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
  }

if(logged_in())
  send_user_to("/user/" . $_SESSION['username'],"ureddit.com");

?>
<html>
<head>
<?php include('favicon.html'); ?>
<title>University of Reddit</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<? require('header.php'); ?>
<div id="main">
  <h2>login</h2><br />
  <form method="post" action="<?=PREFIX ?>/login">

  username:<br />
  <input type="text" name="username" id="username" /><br /><br />

  password:<br />
  <input type="password" name="password" id="password" /><br /><br />

  <input type="submit" />
  </form><br />

  <a href="<?=PREFIX ?>/recover_password">Forgot your password?</a>
</div>

<?php require('footer.php'); ?>

</body>
</html>
