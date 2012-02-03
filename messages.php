<?php

require_once('init.php');

if(!logged_in())
    send_user_to("/");

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
    Inbox | <a href="<?=PREFIX ?>/messages/sent">Outbox</a>
  </div>

  <div class="desc" style="margin-bottom: 30px;">
    <?php
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $user = new user($dbpdo, $_SESSION['user_id']);
    display_messages($user, $offset, $pagesize);
    ?>
  </div>

  <div style="font-size: 0.75em; margin-left: 15px;">
  <?php
    if($offset >= $pagesize)
    {
      ?>  <a href="?offset=<?=$offset - $pagesize ?>" class="link-class-desc" style="font-size: 1.5em;">previous</a><?php
    }

    if(num_messages($user) > $offset + $pagesize)
    {
      ?>  <a href="?offset=<?=$offset + $pagesize ?>"class="link-class-desc" style="font-size: 1.5em;">next</a><?php
    }
   ?>
   </div>
</div>

<?php require('footer.php'); ?>

</body>
</html>