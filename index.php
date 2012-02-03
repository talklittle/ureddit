<?php
require('init.php');
?>
<!DOCTYPE html>
<html>
<head>
<?php include('favicon.html'); ?>
<meta charset=UTF-8>
<title>University of Reddit</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=PREFIX ?>/jquery-1.4.2.min.js"></script>
</head>

<body>
<? require('header.php'); ?>
<div id="main">

  <div style="width:780px; margin: 10px 10px 10px 10px; padding: 10px; background-color: #FDED93; border: 1px solid #FFAE00">
  Follow <a href="http://twitter.com/uofreddit">@uofreddit on Twitter</a> for new class announcements, existing class' updates, site changes, and more!
  </div>
  <div class="pagetitle">
    Classes
  </div>
  <?php

  $catalog = new catalog($dbpdo);
  $catalog->display();

  ?>
</div>

<?php require('footer.php'); ?>

</body>
</html>
