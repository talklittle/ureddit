<?php
require_once('../init.php');
$params['title'] .= ' : Taught Classes';
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
