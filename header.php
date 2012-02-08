<? require_once('init.php'); ?>
<div id="header">
  <a href="<?=PREFIX ?>/">
    <img src="<?=PREFIX ?>/images/logo.png" alt="University of Reddit" id="logo" />
  </a>
  <div id="links">
    <a href="<?=PREFIX ?>/" class="<?=(PREFIX."/index.php" == $_SERVER['PHP_SELF'] ? "nav-current" : "nav") ?>">course catalog</a> - 
   <?php

   $teacher = $dbpdo->query("SELECT COUNT(*) FROM `associations` WHERE `parent_id` = ? AND `type` = ?", array($dbpdo->session('user_id'),'teacher'));

     if($teacher[0]['COUNT(*)'] == "0") {
       ?><a href="<?=PREFIX ?>/teach" class="<?=(PREFIX."/teach.php" == $_SERVER['PHP_SELF'] ? "nav-current" : "nav") ?>">become an instructor</a> - <?php
     } else {
       ?><a href="<?=PREFIX ?>/teachers/" class="<?=(PREFIX."/teachers/index.php" == $_SERVER['PHP_SELF'] ? "nav-current" : "nav") ?>">teacher admin panel</a> - <?php
     }
   ?>
   <a href="http://reddit.com/r/UniversityOfReddit" class="nav">/r/UniversityofReddit</a> - 
    <a href="http://twitter.com/uofreddit" class="nav">@uofreddit</a> - 
   <?php
  if(logged_in())
{
  ?>
  <a href="<?=PREFIX ?>/user/<?=$_SESSION['username'] ?>" class="<?=(PREFIX."/user.php" == $_SERVER['PHP_SELF'] && isset($_GET['id']) && $_GET['id'] == $_SESSION['username'] ? "nav-current" : "nav") ?>">
    <?=$_SESSION['username'] ?>
  </a>
  <a href="<?=PREFIX ?>/messages" class="nav"><img src="<?=PREFIX ?>/images/<?=(has_new_messages($dbpdo, $_SESSION['user_id']) ? "new_messages.png" : "messages.png") ?>" style="border: 0;" id="message" /></a> 
  (<a href="<?=PREFIX ?>/settings" class="nav<?=(PREFIX."/preferences.php" == $_SERVER['PHP_SELF'] ? "-current" : "" )?>">settings</a> - 
<a href="<?=PREFIX ?>/logout" class="nav">logout</a>) - 
  <?php
} else {
   ?>
   <a href="<?=PREFIX ?>/register" class="<?=(PREFIX."/register.php" == $_SERVER['PHP_SELF'] ? "nav-current" : "nav") ?>">register</a> - 
   <a href="<?=PREFIX ?>/login" class="<?=(PREFIX."/login.php" == $_SERVER['PHP_SELF'] ? "nav-current" : "nav") ?>">login</a> - 
     <?php
     }
?>
    <a href="<?=PREFIX ?>/help" class="<?=(PREFIX."/help.php" == $_SERVER['PHP_SELF'] ? "nav-current" : "nav") ?>">faq</a>
  </div>
</div>