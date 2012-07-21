<?php
require_once('init.php');

if(!logged_in())
    send_user_to("/");

$pagesize = 25;
$params['title'] .= ' : Outbox';
require('header2.php');

$user = new user($dbpdo, $dbpdo->session('user_id'));
  ?>
    <div id="outbox">
      <div class="content">
        [<a href="<?=PREFIX ?>/messages">inbox</a>]<h1>Outbox</h1> 
        <?php
        display_sent_messages($user);
        ?>
      </div>
    </div>
<?php require_once('footer2.php'); ?>
