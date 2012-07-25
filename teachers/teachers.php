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

if($class->owner != $user->id)
  send_user_to("/class/" . $class->id . "/" . $class->seo_string($class->value));

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

$params['title'] .= ' Manage Teachers';
require('../header2.php');

?>
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
  <?php require_once('../footer.php'); ?>
