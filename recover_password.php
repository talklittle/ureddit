<?php

require_once('init.php');

if(logged_in())
  send_user_to("/user/" . $_SESSION['username']);

$error = array();
$success = false;
if(!empty($_POST))
{
  $email = $_POST['email'];

  if(strlen($email) == 0)
    $error[] = "You must enter an email address.";
  else
    {
      if(!preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $_POST['email']))
	$error[] = "It seems that you entered an invalid email address.";
      else {
	$users = $dbpdo->query("SELECT objects.id AS id  FROM objects INNER JOIN object_attributes ON object_attributes.type = 'email' AND object_attributes.value = ? AND objects.id = object_attributes.object_id LIMIT 1", array($email));
	if(count($users) > 0)
	  {
	    $user = new user($dbpdo, $users[0]['id']);
	    $newpass = generate_random_password();
	    $hash = $user->crypt_password($newpass, $user->id);
	    $user->define_attribute('password_crypt', $hash, 0);
	    $user->save();

	    $headers = 'From: no-reply@universityofreddit.com';
	    mail($email,"Your new University of Reddit password","The new password for the University of Reddit account named " . $user->value . " registered with this email address is:\n\n$newpass",$headers);
	    if(config::postfix)
	      {
		$emailpass = pacrypt(escape_string($newpass));
		$dbpdo->query("UPDATE pf_mailbox SET password = ? WHERE username = ?", array($emailpass, $user->value . "@ureddit.com"));
	      }

	    $success = true;
	  }
      }
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
    <div id="settings">
      <div class="content">
      <h1>Password Reset</h1>
  If you registered your old account with an email, we can email you a new password there. If you didn't, then, well, you're out of luck - we have no way of knowing whether it's really your account. Make another.
<br /><br />

<form method="post" action="<?=PREFIX ?>/recover_password">
Your email address:<br />
<input type="text" name="email" size="40" /><br /><br />
<input type="submit" value="Send me a new password!" />
</form><br />

<?php
if($success)
   echo "<strong>E-mail sent!</strong>";
elseif(count($error) > 0)
  foreach($error as $err)
    echo "<span style=\"color: red;\">$err</span><br />\n";
?>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
