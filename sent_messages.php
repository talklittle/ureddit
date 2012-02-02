<?php

require_once('init.php');

if(!logged_in())
    send_user_to("/login");
 
$pagesize = 25;
 
?>
<!DOCTYPE html>
<html>
<head>
<?php include('favicon.html'); ?>
<meta charset=UTF-8>
<title>University of Reddit</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jquery-1.4.2.min.js"></script>
</head>
 
<body>
<? require('header.php'); ?>
<div id="main">
  <div class="pagetitle">
  <a href="<?=PREFIX ?>/messages">Inbox</a> | Outbox
  </div>
 
  <div class="desc" style="margin-bottom: 30px;">
    <?php
    $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
    display_sent_messages(new user($dbpdo,$_SESSION['user_id']), $offset, $pagesize);
    ?>
  </div>
 
  <div style="font-size: 0.75em; margin-left: 15px;">
  <?php
    if($offset >= $pagesize)
    {
      ?>  <a href="?offset=<?=$offset - $pagesize ?>" class="link-class-desc" style="font-size: 1.5em;">previous</a><?php
    }
 
    if(num_sent_messages($_SESSION['user_id']) > $offset + $pagesize)
    {
      ?>  <a href="?offset=<?=$offset + $pagesize ?>" class="link-class-desc" style="font-size: 1.5em;">next</a><?php
    }
   ?>
   </div>
</div>

<?php include('footer.php'); ?>
 
</body>
</html>