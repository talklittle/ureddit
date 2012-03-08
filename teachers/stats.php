<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$class = new course($dbpdo, $_GET['id']);
$user = new user($dbpdo, $_SESSION['user_id']);

$class->get_teachers();
$class->get_owner();

if($class->owner != $user->id || !in_array($user->id,$class->teachers))
  send_user_to("/teachers/index.php");


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

  <script src="<?=PREFIX ?>/js/libs/modernizr-2.5.2.min.js"></script>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('../header.php');
  require_once('../social.php');

  ?>
  <div id="main" role="main">
    <div id="teach">
      <div class="content">
        <h1>Statistics: <?=$class->value ?></h1>

        <?php
    $impressions = $class->dbpdo->query("SELECT COUNT(*) FROM `views` WHERE `displayed_object_id` = ?", array($class->id));
    $expanded = $class->dbpdo->query("SELECT COUNT(*) FROM `views` WHERE `displayed_object_id` = ? AND `comments` = ?", array($class->id, 'expanded'));
    $full = $class->dbpdo->query("SELECT COUNT(*) FROM `views` WHERE `displayed_object_id` = ? AND `comments` = ?", array($class->id, 'expanded;full'));
$mass_messages = $class->dbpdo->query("SELECT COUNT(*) FROM `activity` WHERE `child_id` = ? AND `action` LIKE ?", array($class->id, 'mass%'));
$num_read = $class->dbpdo->query("SELECT COUNT(*) FROM `associations` WHERE `parent_id` = ? AND `type` = ?", array($class->id, 'read_mass_message'));
$num_unread = $class->dbpdo->query("SELECT COUNT(*) FROM `associations` WHERE `parent_id` = ? AND `type` = ?", array($class->id, 'unread_mass_message'));
$adds = $class->dbpdo->query("SELECT COUNT(*) FROM `activity` WHERE `child_id` = ? AND `action` = ?", array($class->id, 'added class'));
$drops = $class->dbpdo->query("SELECT COUNT(*) FROM `activity` WHERE `child_id` = ? AND `action` = ?", array($class->id, 'dropped class'));
$upvotes = $class->dbpdo->query("SELECT COUNT(*) FROM `activity` WHERE `child_id` = ? AND `action` = ?", array($class->id, 'upvoted'));
$downvotes = $class->dbpdo->query("SELECT COUNT(*) FROM `activity` WHERE `child_id` = ? AND `action` = ?", array($class->id, 'downvoted'));
        ?>
      <strong>Impressions:</strong> <?=$impressions[0]['COUNT(*)'] ?><br />
      <strong>Expanded views:</strong> <?=$expanded[0]['COUNT(*)'] ?><br />
      <strong>Class Page views:</strong> <?=$full[0]['COUNT(*)'] ?><br /><br />

      <strong>Adds:</strong> <?=$adds[0]['COUNT(*)'] ?><br />
      <strong>Drops:</strong> <?=$drops[0]['COUNT(*)'] ?><br /><br />

      <!--
      <strong>Upvotes:</strong> <?=$upvotes[0]['COUNT(*)'] ?><br />
      <strong>Downvotes:</strong> <?=$downvotes[0]['COUNT(*)'] ?><br /><br />
      -->

      <strong>Distinct mass PMs:</strong> <?=$mass_messages[0]['COUNT(*)'] ?><br />
      <strong>Percent of all mass PMs that have been read:</strong> <?=$num_read[0]['COUNT(*)'] + $num_read[0]['COUNT(*)'] == 0 ? 'unavailable' : 100*round($num_read[0]['COUNT(*)']/($num_read[0]['COUNT(*)'] + $num_unread[0]['COUNT(*)']),4) . '%' ?><br />

  <br /><br />
      </div>
    </div>
    <div id="teach-side">
      <div class="content" style="border-bottom: 3px solid #232323">
        <h2>Note:</h2>

  Class statistics only take into account 7 March 2012 onwards. Furthermore, some data has only been collected since its corresponding feature was implemented, such as voting. If your class was created prior to 7 March 2012, the statistics gives to the left will be incomplete.

      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('../footer.php'); ?>
</body>
</html>
