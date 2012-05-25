<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$user = new user($dbpdo, $_SESSION['user_id']);
$class = new course($dbpdo, $_GET['id']);
$class->get_owner();
$class->get_teachers();
$class->get_categories();
$class->get_attributes();

$teacher_check = @mysql_fetch_assoc(mysql_query("SELECT COUNT(*) FROM `classes` WHERE `id`='$class_id' AND `teacher_id`='$user_id'"));
if($class->owner != $user->id)
  send_user_to("/teachers");

$error = array();
if(!empty($_POST))
  {
    $id = object_type_value_to_id($dbpdo, 'user',$_POST['username']);
    if(count($id) == 0)
      $error[] = "there is no user with that username";
    else
      {
	$class->add_teacher($id[0]['id']);
	$user->message((int)$id[0]['id'],"You've been added as a teacher!","User " . $user->value . " has added you as a teacher for the class \"[" . $class->value . "](http://ureddit.com/c" . $class->id . ")\".");
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
  <?php include('../includes.php'); ?>
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
        <h1>Manage teachers</h1>
<?php

foreach($class->teachers as $teacher_id)
  {
    $teacher = new user($class->dbpdo, $teacher_id);
    ?><a href="<?=PREFIX ?>/user/<?=$teacher->value ?>"><?=$teacher->value ?></a> <?php
    if($teacher->id == $user->id)
      {
	echo '<em>(that\'s you!)</em> ';
      }

    if($class->owner != $teacher->id)
      {
	?> - <small>[<a href="<?=PREFIX ?>/teachers/remove_teacher.php?id=<?=$class->id ?>&username=<?=$teacher->value ?>">remove</a>]</small><?php
      }
    ?><br><?php
  }
?>

<form method="post" action="<?=PREFIX ?>/class/<?=$class->id ?>/teachers">
<input type="text" class="teach" name="username" id="username"> <input type="submit" value="add teacher">
</form>
      </div>
    </div>
    <div id="teach-side">
      <div class="content">
        <h2>Help</h2>
    <p>UReddit allows you to add other teachers with whom you would like to work to your class. You are able to remove any of them at any time you like, but they can never remove you because you made the class.</p>

<p>Each teacher you add  will have full access to all teacher admin panel tools.</p>

    <?php include('tools.php'); ?>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('../footer.php'); ?>
</body>
</html>
