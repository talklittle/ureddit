<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$user = new user($dbpdo, $_SESSION['user_id']);
$lecture = new lecture($dbpdo, $_GET['id']);
$class = new course($dbpdo, $lecture->class_id);
$class->get_owner();
$class->get_teachers();
$class->get_categories();
$class->get_attributes();

$teacher_check = @mysql_fetch_assoc(mysql_query("SELECT COUNT(*) FROM `classes` WHERE `id`='$class_id' AND `teacher_id`='$user_id'"));
if($class->owner != $user->id && !in_array($user->id, $class->teachers))
  send_user_to("/teachers");

$error = array();
if(!empty($_POST))
{

  if(trim(strlen($_POST['title'])) == 0)
    $error[] = "Please enter a lecture title.";

  if(trim(strlen($_POST['desc'])) == 0)
    $error[] = "Please enter a lecture description.";

  if(count($error) == 0)
    {
      $lecture->update_value($_POST['title']);
      $lecture->set_description($_POST['desc']);
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
        <h1>Edit lecture for <?=$class->value ?></h1>
    <p>UReddit uses Markdown for formatting text, just like Reddit. <a href="http://old-wp.slekx.com/the-markdown-tutorial/" target="_blank">Here</a> is an online tutorial if you are unfamiliar with the syntax.</p>

<div style="color: red;">
<?php
if(count($error) != 0)
  foreach($error as $err)
    echo "- $err<br />\n";
?>
</div><br />

<form method="post" action="<?=PREFIX ?>/teachers/edit_lecture.php?id=<?=$lecture->id ?>">
Lecture title:<br />
<input type="text" name="title" class="teach" value="<?=post('title',$lecture->value) ?>" />
<br /><br />

Lecture description:<br />
<textarea name="desc" id="desc" class="teach"><? try { echo post('desc',$lecture->get_attribute_value('description')); } catch ( ObjectAttributeNotFoundException $e) {} ?></textarea>
<br /><br />

<input type="submit" style="padding: 3px;" />
</form>
      </div>
    </div>
    <div id="teach-side">
      <div class="content">
    <?php include('tools.php'); ?>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('../footer.php'); ?>
</body>
</html>
