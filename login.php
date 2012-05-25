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

?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>University of Reddit : Log In</title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <?php include('includes.php'); ?>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('header.php');
  require_once('social.php');

  if(isset($_GET['category_id']) && count($dbpdo->query("SELECT `id` FROM `objects` WHERE `id` = ? AND `type` = 'category' LIMIT 1", array($_GET['category_id'])) != 0))
    $active_category_id = $_GET['category_id'];
  else
    $active_category_id = -1;

  $catalog = new catalog($dbpdo);
  ?>
  <div id="main" role="main">
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
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
