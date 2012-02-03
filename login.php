 <?php

require_once('init.php');
$loginmsg = "";

if(!empty($_POST))
  {
    $user = new user($dbpdo);
    if($user->verify_credentials($_POST['username'], $_POST['password']))
      {
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
