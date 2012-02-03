<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$user = new course($dbpdo, $_SESSION['user_id']);
$class = new course($dbpdo, $_GET['id']);
$class->get_teachers();
$class->get_categories();

$teacher_check = @mysql_fetch_assoc(mysql_query("SELECT COUNT(*) FROM `classes` WHERE `id`='$class_id' AND `teacher_id`='$user_id'"));
if($class->owner != $user->id && !in_array($user->id, $class->teachers))
  send_user_to("/teachers");

$error = array();
if(!empty($_POST))
{
   $name = $_POST['name'];
   $desc = $_POST['desc'];
   $syllabus = $_POST['syllabus'];
   $prereq = $_POST['prereq'];
   $addinfo = $_POST['addinfo'];
   $cat_id = $_POST['category'];
   $url = $_POST['url'];
   $live = $_POST['live'];
   $qualifications = $_POST['qualifications'];
   $status = $_POST['status'];

   if(strlen($url) > 0 && !preg_match('/^((http|https|irc):\/\/).*/',$url))
      $error[] = "Your class URL must either be empty or start with http://, https://, or irc://.";

   if(strlen($name) == 0)
   {
     $error[] = "You must enter a course name.";
   }

   if(strlen($desc) == 0)
     $error[] = "You must enter a course description.";

   if(count($error) == 0)
   {
     if(!in_array($cat_id, $class->categories))
       {
	 foreach($class->categories as $id)
	   $class->remove_from_category($id);
	 $class->add_to_category($cat_id);
       }

     try
       {
	 $old_status = $class->get_attribute_value('status');
       }
     catch( ObjectAttributeNotFoundException $e)
       {
	 $old_status = '0';
       }

     $class->update_value($name);
     $class->define_attribute('description', $desc, 0);
     $class->define_attribute('syllabus', $syllabus, 0);
     $class->define_attribute('prerequisites', $prereq, 0);
     $class->define_attribute('additional_information', $addinfo, 0);
     $class->define_attribute('url', $url, 0);
     $class->define_attribute('live', $live, 0);
     $class->define_attribute('teacher_qualifications', $qualifications, 0);
     $class->define_attribute('status', $status, 0);

     $class->save();

     $memcache = new Memcache;
     $memcache->connect("localhost", 11211);
     $memcache->delete("v3_catalog-expanded");
     $memcache->delete("v3_catalog-collapsed");
     $memcache->delete("v3_class" . $class_id);

     if($status == 0 && $old_status != 0)
       {
	 $tweet = "(" . date("U") . ") Unfortunately, " . $user->value . " has cancelled \"$name\".";
	   if(strlen($tweet) > 140)
	     $tweet = substr($tweet,0,135) . "...\".";
	   tweet($tweet);
	 }
       else
	 {
	   $tweet = "(" . date("U") . ") " . $user->value . " has updated their class: http://ureddit.com" . PREFIX . "/c" . $class->id;
	   if(strlen($tweet) < 134)
	     {
	       $tweet .= " \"$name\"";
	       if(strlen($tweet) > 140)
		 $tweet = substr($tweet, 0, 136) . "...\"";
	     }
	   
	   tweet($tweet);
	 }
     
     send_user_to("/class/" . $class->id . "/edit");
   }
}

?>
<!DOCTYPE html>
<html>
<head>
<?php include('../favicon.html'); ?>
<meta charset=UTF-8>
<title>University of Reddit</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<? require('../header.php'); ?>
<div id="main">
  <div class="pagetitle">
    Edit class: <?=htmlspecialchars($class->value) ?>
  </div>

<div style="color: red;">
<?php
if(count($error) != 0)
  foreach($error as $err)
    echo "- $err<br />\n";
?>
</div><br />

<form method="post" action="<?=PREFIX ?>/class/<?=$class->id ?>/edit">
Course name:<br />
<input type="text" name="name" class="teach" value="<?=post('name',$class->value) ?>" />
<br /><br />

Course category:<br />
<? category_dropdown($dbpdo, "category", post('category',$class->categories[0])); ?>
<br /><br />

URL to Reddit post about this course:<br />
<input type="text" name="url" class="teach" value="<?=post('url',$class->get_attribute_value('url')) ?>" />
<br /><br />

Course description:<br />
<textarea name="desc" class="teach"><?=post('desc',$class->get_attribute_value('description')) ?></textarea>
<br /><br />

Syllabus:<br />
<textarea name="syllabus" class="teach"><?=post('syllabus',$class->get_attribute_value('syllabus')) ?></textarea>
<br /><br />

Course prerequisites:<br />
<textarea name="prereq" class="teach"><?=post('prereq',$class->get_attribute_value('prerequisites')) ?></textarea>
<br /><br />

Additional information:<br />
<textarea name="addinfo" class="teach"><?=post('addinfo',$class->get_attribute_value('additional_information')) ?></textarea>
<br /><br />

Teacher qualifications:<br />
<textarea name="qualifications" class="teach"><?=post('qualifications',$class->get_attribute_value('teacher_qualifications')) ?></textarea>
<br /><br />

Is this a live class?<br />
<select name="live">
<option <? echo $class->get_attribute_value('live') == "true" ? "SELECTED" : "" ?> value="true">Yes</option>
<option <? echo $class->get_attribute_value('live') == "false" ? "SELECTED" : "" ?> value="false">No</option>
</select><br /><br />

What is the status of your class?<br />
  <select name="status">
  <option <? echo $class->get_attribute_value('status') == "1" ? "SELECTED" : "" ?> value="1">has not begun, open for signups</option>
  <option <? echo $class->get_attribute_value('status') == "2" ? "SELECTED" : "" ?> value="2">has not begun, closed to signups</option>
  <option <? echo $class->get_attribute_value('status') == "3" ? "SELECTED" : "" ?> value="3">running and open for signups</option>
  <option <? echo $class->get_attribute_value('status') == "4" ? "SELECTED" : "" ?> value="4">running but closed to signups</option>
  <option <? echo $class->get_attribute_value('status') == "5" ? "SELECTED" : "" ?> value="5">completed</option>
  <option <? echo $class->get_attribute_value('status') == "0" ? "SELECTED" : "" ?> value="0">cancelled</option>
</select><br /><br />

<input type="submit" style="padding: 3px;" />
</form>

</div>

</body>
</html>
