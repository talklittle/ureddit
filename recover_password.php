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
		$emailpass = pacrypt(escape_string($newpass), $user->id);
		$dbpdo->query("UPDATE pf_mailbox SET password = ? WHERE username = ?", array($emailpass, $user->value . "@ureddit.com"));
	      }

	    $success = true;
	  }
      }
    }
}


$params['title'] .= ' : Password Recovery';
require('header2.php');
?>
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
<?php require_once('footer2.php'); ?>
