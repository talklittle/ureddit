<?php
require('../init.php');
?>
<!DOCTYPE html>
<html>
<head>
<?php include('../favicon.html'); ?>
<meta charset=UTF-8>
<title>University of Reddit</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../jquery-1.4.2.min.js"></script>
</head>

<body>
<? require('../header.php'); ?>
<div id="main">
  <div class="pagetitle">
    Your Classes <span style="font-size: 0.6em; font-weight: normal; color: black;">[<a href="<?=PREFIX ?>/teach" style="color: black;">create new class</a>]</span>
  </div>
  <?php
  list_teacher_classes(new user($dbpdo, $_SESSION['user_id']));
  ?>
</div>
<?php require('../footer.php'); ?>
</body>
</html>
