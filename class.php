<?php

require_once('init.php');

if((int)$_GET['id'] < 2000)
  send_user_to("/class/" . translate_class_id($dbpdo, $_GET['id']));

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
  die();
  ?>
      <div class="class" style="width: 800px;">
      <? /*signup_button($class['id']);*/ ?>
	<div class="class-name">
	<?=htmlspecialchars(stripslashes($class->value)) ?>
       <?php
  try
{
       if($class->get_attribute_value('live') == 'true')
         {
           ?>
           <img src="<?=PREFIX ?>/images/live.png" alt="live lectures!" style="height: 0.8em; margin-left: 3px;" /> <span style="font-style: italic; font-weight: normal; font-size: 0.8em;">this is a live class!</span><br />
           <?php
         }
}
catch (ObjectAttributeNotFoundException $e)
{

}
?>
	</div>
	<div class="class-desc">
        <?php
        try
	  {
	    process(stripslashes($class->get_attribute_value('description')));
	  }
        catch (ObjectAttributeNotFoundException $e)
	  {
	    echo "<em>no description</em>";
	  }
        ?>
	</div>
	<div class="class-info">
         taught by <a href="<?=PREFIX ?>/user/<?=$teacher->value ?>" class="link-class-desc"><?=$teacher->value ?></a> <?php
  try
	{
                 ?>
                 [<a href="http://reddit.com/user/<?=$teacher->get_attribute_value('reddit_username') ?>" class="link-class-desc">teacher reddit user page</a>]
                 <?php
           }
catch (ObjectAttributeNotFoundException $e)
{
}

try
{
  echo "[<a href=\"" . htmlspecialchars(stripslashes($class->get_attribute_value('url'))) . "\" class=\"link-class-desc\">class URL</a>] ";
}
catch (ObjectAttributeNotFoundException $e)
{
}
       if(exec("ls files | grep class" . $class->id))
          echo "[<a href=\"/class/" . $class->id . "/files\" class=\"link-class-desc\">class files</a>] ";
if(logged_in())
  {
?>
          <span id="report<?=$class->id ?>">[<a href="#" class="link-class-desc" onclick="$.post('<?=PREFIX ?>/report.php',{class: <?=$class->id ?>},function(response){$('#report<?=$class->id ?>').html(response); return false;});">report class]</a></span>
<?php
	    }
       ?>

	</div>
      <br /><br />

      <div class="class-name">
        Prerequisites
      </div>
      <div class="class-desc">
      <?php
      try
        {
          echo process(stripslashes($class->get_attribute_value('prerequisites')));
	}
      catch (ObjectAttributeNotFoundError $e)
        {
          echo "<em>none</em>";
	}

      ?>
      </div>
      <br /><br />

      <div class="class-name">
        Syllabus
      </div>
      <div class="class-desc">
      <?php
      try
        {
          echo process(stripslashes($class->get_attribute_value('syllabus')));
	}
      catch (ObjectAttributeNotFoundError $e)
        {
          echo "<em>none</em>";
	}
      ?>
      </div>
      <br /><br />

      <div class="class-name">
        Additional information
      </div>
      <div class="class-desc">
      <?php
      try
        {
          echo process(stripslashes($class->get_attribute_value('additional_information')));
	}
      catch (ObjectAttributeNotFoundError $e)
        {
          echo "<em>none</em>";
	}
      ?>
      </div>
      <br /><br />

      <div class="class-name">
        Teacher qualifications
      </div>
      <div class="class-desc">
      <?php
      try
        {
          echo process(stripslashes($class->get_attribute_value('teacher_qualifications')));
	}
      catch (ObjectAttributeNotFoundError $e)
        {
          echo "<em>not given</em>";
	}
      ?>
      </div>
      <br /><br />

      <div class="class-name">
        Roster
      </div>
      <div class="class-desc">
      <?php
      $class->get_roster();
      $count = 0;
      foreach($class->roster as $user_id)
	{
	  $user = new user($dbpdo, $user_id);
	  echo ++$count . '. <a href="' . PREFIX . '/user/' . $user->value . '" style="color: black;">' . $user->value . '</a>';
	  try
	    {
	      echo ' <a href="http://www.reddit.com/message/compose/?to=' . $user->get_attribute_value('reddit_username') . '"><img src="' . PREFIX . '/images/reddit.png" style="border: 0; height: 1em;" /></a>';
	    }
	  catch(ObjectAttributeNotFoundException $e)
	    {
	      
	    }
	  echo '<br />';
	}
      if($count == 0)
	{
	  echo "<em>no students found</em>";
	}
  ?>
      </div>
      </div>
</div>

<?php require('footer.php'); ?>

</body>
</html>
