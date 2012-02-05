<?php

require_once('init.php');

if((int)$_GET['id'] < 2000)
  {
    if($id = translate_class_id($dbpdo, $_GET['id']))
      send_user_to("/class/" . $id,"ureddit.com","301 Moved Permanently");
  }

try
  {
    $class = new course($dbpdo, $_GET['id']);
  }
catch (CourseNotFoundException $e)
  {
    send_user_to("/");
  }

?>
<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>University of Reddit</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=PREFIX ?>/jquery-1.4.2.min.js"></script>
<?php include('favicon.html'); ?>
</head>

<body>
<? require('header.php'); ?>
<div id="main">
  <div class="pagetitle">
    View class: <?=htmlspecialchars(stripslashes($class->value)) ?>
  </div>

  <?php
  $class->display(true, true);
  require('footer.php');
  ?>
</body>
</html>
