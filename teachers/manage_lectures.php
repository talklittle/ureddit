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
if($class->owner != $user->id && !in_array($user->id, $class->teachers))
  send_user_to("/teachers");

$error = array();
if(!empty($_POST))
{
  print_r($_POST);

  $title = $_POST['title'];
  $desc = $_POST['desc'];
  $order = $_POST['order'];

  if(strlen(trim($title)) == 0)
    $error[] = "Please enter a lecture title.";

  if(strlen(trim($desc)) == 0)
    $error[] = "Please enter a lecture description.";

  if(count($error) == 0)
    {
      $lecture = new lecture($class->dbpdo);
      $lecture->define('lecture',$title,0);
      $lecture->save();
      
      $lecture->set_description($desc);
      $lecture->set_order($order);
      $lecture->save();
      
      $class->add_lecture($lecture->id);
    }
}

$params['title'] .= ' : Manage Lectures';
require('../header2.php');
?>
    <div id="teach">
      <div class="content">
        <h1>Lectures</h1>
    <p>UReddit uses Markdown for formatting text, just like Reddit. <a href="http://old-wp.slekx.com/the-markdown-tutorial/" target="_blank">Here</a> is an online tutorial if you are unfamiliar with the syntax.</p>

<div style="color: red;">
<?php
if(count($error) != 0)
  foreach($error as $err)
    echo "- $err<br />\n";
?>
</div><br />

<?php

$class->display_lectures(true);

?>
      </div>
    </div>
    <div id="teach-side">
      <div class="content">
    <?php include('tools.php'); ?>
      </div>
    </div>
<?php require_once('../footer2.php'); ?>
