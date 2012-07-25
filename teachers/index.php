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

if($class->owner != $user->id && !in_array($user->id, $class->teachers))
  send_user_to("/class/" . $class->id . "/" . $class->seo_string($class->value));

$params['title'] .= ' : My Classes';
require('../header2.php');
?>
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
<?php require_once('../footer2.php'); ?>
