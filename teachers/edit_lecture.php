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

$params['title'] .= ' : Edit Lecture';
require('../header2.php');

?>
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
<?php require_once('../footer2.php'); ?>
