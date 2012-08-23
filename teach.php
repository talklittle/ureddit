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

   $blacklist = array(
		      "manhoodacademy"
		      ,"manhood101"
		      ,"nigger"
		      ,"game_of_trolls"
		      ,"gameoftrolls"
		      ,"seigheil"
		      ,"seig heil"
		      );
   $haystack = strtolower($plain_name . $desc . $syllabus . $prereq . $addinfo . $url . $qualifications);
   foreach($blacklist as $black)
     {
       if(strpos($haystack, $black) !== FALSE)
	 {
	   $error[] = "Please fill out all fields appropriately.";
	   break;
	 }
     }
   
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

   $teacher = new user($dbpdo, $_SESSION['user_id']);
   $teacher->get_taught_classes();
   if(count($teacher->teaching) > 0)
     {
       foreach($teacher->teaching as $id)
	 {
	   $class = new course($dbpdo, $id);
	   $now = date("U");
	   $creation = date("U",$class->creation);
	   if($now - $creation < 300)
	     $error[] = "You are creating classes too quickly.";
	 }
     }
   
   if(count($error) == 0)
   {
     $class = new course($dbpdo);
     $class->define('class',$name,0);
     $class->save();
     $teacher->log_to_feed('created class', $class->id);
     
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
     tweet($teacher->config,$tweet);
     send_user_to("/class/" . $class->id . "/admin");
   }
}

$params['title'] .= ' : Teach a Class';
require('header2.php');

?>
    <div id="teach">
      <div class="content">
        <h1>Create a class</h1>

    <p>UReddit uses Markdown for formatting text, just like Reddit. <a href="http://old-wp.slekx.com/the-markdown-tutorial/" target="_blank">Here</a> is an online tutorial if you are unfamiliar with the syntax.</p>

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
    </div>
    <div id="teach-side">
      <div class="content">
        <h2>READ ME!</h2>

        Thank you for considering offering a class, but, please, consider the following first:

        <ul>
          <li>
            You are making a commitment to everyone that signs up for your class. Are you <strong>sure</strong> that you will be able to follow through and complete this class? Are you <strong>sure</strong> that you will consistently have the free time?
          </li>
          <li>
            Consider preparing a sizable portion of the material for your class beforehand in order to be prepared for any unforseen circumstances.
          </li>
          <li>
    Are you aware of all the resources available to you? You should have read the <a href="<?=PREFIX ?>">help</a> page for a list of resources including, but not limited to, the UReddit voice chat servers, @ureddit.com email addresses, third-party hosting platforms, third-party video streaming services, &c.
          </li>
          <li>
    If someone were to challenge your credentials and/or qualifications for teaching the material you plan to teach, will you be able to demonstrate your competence?
          </li>
          <li>
            How do you plan to keep students engaged?
          </li>
          <li>
            Have you already gauged interest in this class by making an [Offer] post in <a href="http://reddit.com/r/UniversityofReddit">our subreddit</a>?
          </li>
        </ul>
      </div>
    </div>
<?php require_once('footer2.php'); ?>
