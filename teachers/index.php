<?php
require_once('../init.php');
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
  <link rel="stylesheet" href="<?=PREFIX ?>/css/style.css">
  <?php include('../fonts.php'); ?>

  <script src="<?=PREFIX ?>/js/libs/modernizr-2.5.2.min.js"></script>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('../header.php');
  require_once('../social.php');
  ?>
  <div id="main" role="main">
    <div id="my-classes">
      <div class="content">
          <h2>My classes</h2>
	  <?php
            list_teacher_classes(new user($dbpdo, $dbpdo->session('user_id')));
          ?>
      </div>
    </div>
    <div id="my-statistics">
      <div class="content">
        <h2>Create Class</h2>
        <p>
          <a href="<?=PREFIX ?>/teach">I'd like to teach one more!</a>
        </p>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('../footer.php'); ?>
</body>
</html>
