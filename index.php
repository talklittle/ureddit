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
  <span class="class" style="font-size: 0.5em; font-weight: normal; padding: 5px;">filter by status: <a href="<?=PREFIX ?>/filter/open" <?=$_GET['show']=='open' ? 'style="font-weight:bold; text-decoration: none;"' : '' ?>">open</a> | <a href="<?=PREFIX ?>/filter/completed" <?=$_GET['show']=='completed' ? 'style="font-weight:bold; text-decoration: none;"' : '' ?>">completed</a> | <a href="<?=PREFIX ?>/filter/all" <?=$_GET['show']=='all' ? 'style="font-weight:bold; text-decoration: none;"' : '' ?>">all</a></span>
  </div>
  <?php

  $catalog = new catalog($dbpdo);
  $catalog->display(true, $_GET['show']);

  ?>
</div>

<?php require('footer.php'); ?>

</body>
</html>
