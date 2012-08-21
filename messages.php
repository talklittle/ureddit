<?php
require_once('init.php');

if(!logged_in())
    send_user_to("/");

$pagesize = 25;

$params['title'] .= ' : Inbox';
require('header2.php');

$user = new user($dbpdo, $dbpdo->session('user_id'));
  ?>
    <div id="inbox">
      <div class="content">
        <a href="<?=PREFIX ?>/messages/sent">[outbox]</a>
        <h1>Inbox</h1>
	<?php
        display_messages($user);
        ?>
      </div>
    </div>
<?php require_once('footer2.php'); ?>
