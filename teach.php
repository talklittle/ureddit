<?php

require_once('init.php');

if(!logged_in())
{
    send_user_to("/login");
}

$error = array();
if(!empty($_POST))
{
   $name = $_POST['name'];
   $plain_name = stripslashes($_POST['name']);
   $desc = $_POST['desc'];
   $syllabus = $_POST['syllabus'];
   $prereq = $_POST['prereq'];
   $addinfo = $_POST['addinfo'];
   $cat_id = $_POST['category'];
   $url = $_POST['url'];
   $live = $_POST['live'];
   $qualifications = $_POST['qualifications'];
   
   if(strlen($url) > 0 && !preg_match('/^((http|https|irc):\/\/).*/',$url))
      $error[] = "Your class URL must either be empty or start with http://, https://, or irc://.";

   if(strlen($name) == 0)
   {
     $error[] = "You must enter a course name.";
   } else {
     $check_duplicates = $dbpdo->query("SELECT COUNT(*) FROM `objects` WHERE `type` = 'class' AND `value` = ?", array($name));
     if($check_duplicates[0]['COUNT(*)'] != "0")
       $error[] = "A course with that name already exists.";
   }

   if(strlen($desc) == 0)
     $error[] = "You must enter a course description.";

   if(count($error) == 0)
   {
     $teacher = new user($dbpdo, $_SESSION['user_id']);

     $class = new course($dbpdo);
     $class->define('class',$name,0);
     $class->save();
     
     $class->assign_owner($teacher->id);
     $class->add_teacher($teacher->id);
     $class->add_to_category($cat_id);
     $class->define_attribute('description', $desc, 0);
     $class->define_attribute('syllabus', $syllabus, 0);
     $class->define_attribute('prerequisites', $prereq, 0);
     $class->define_attribute('additional_information', $addinfo, 0);
     $class->define_attribute('url', $url, 0);
     $class->define_attribute('live', $live, 0);
     $class->define_attribute('teacher_qualifications', $qualifications, 0);
     $class->define_attribute('status', '1', 0);
     $class->save();

     $url = "http://ureddit.com" . PREFIX . "/c" . $class->id;
     //$url = make_bitly_url($url);
     $tweet = $teacher->value . " has created a class! $url \"" . $plain_name . "\"";
     if(strlen($tweet) > 140)
       $tweet = substr($config,$tweet, 0, 136) . "...\"";
     tweet($tweet);
     send_user_to("/class/" . $class->id);
   }
}

?>
<!DOCTYPE html>
<html>
<head>
<?php include('favicon.html'); ?>
<meta charset=UTF-8>
<title>University of Reddit</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<? require('header.php'); ?>
<div id="main">
  <div class="pagetitle">
    Create course
  </div>

<div style="color: red;">
<?php
if(count($error) != 0)
  foreach($error as $err)
    echo "- $err<br />\n";
?>
</div><br />

<form method="post" action="<?=PREFIX ?>/teach">
Course name:<br />
<input type="text" name="name" class="teach" value="<?=post('name') ?>" />
<br /><br />

Course category:<br />
  <? category_dropdown($dbpdo, "category", post('category')); ?>
<br /><br />

Class URL - or link to Reddit post about your course:<br />
<input type="text" name="url" class="teach" value="<?=post('url') ?>" />
<br /><br />

Course description:<br />
<textarea name="desc" class="teach"><?=post('desc') ?></textarea>
<br /><br />

Syllabus (make sure to talk about where you will be hosting class materials and when the class will start!):<br />
<textarea name="syllabus" class="teach"><?=post('syllabus') ?></textarea>
<br /><br />

Course prerequisites:<br />
<textarea name="prereq" class="teach"><?=post('prereq') ?></textarea>
<br /><br />

Additional information:<br />
<textarea name="addinfo" class="teach"><?=post('addinfo') ?></textarea>
<br /><br />

Your qualifications:<br />
<textarea name="qualifications" class="teach"><?=post('qualifications') ?></textarea>
<br /><br />

Will you be giving live lectures?<br />
<select name="live">
<option value="true">Yes</option>
<option value="false">No</option>
</select><br /><br />

<input type="submit" style="padding: 3px;" />
</form>

</div>

<?php include('footer.php'); ?>

</body>
</html>
