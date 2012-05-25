<?php
require_once('init.php');

if(!logged_in())
    send_user_to("/");

$pagesize = 25;

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

  $user = new user($dbpdo, $dbpdo->session('user_id'));
  ?>
  <div id="main" role="main">
    <div id="inbox">
      <div class="content">
        [<a href="<?=PREFIX ?>/messages/sent">outbox</a>]
        <h1>Inbox</h1>
	<?php
        display_messages($user);
        ?>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
