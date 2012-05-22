<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$user = new course($dbpdo, $_SESSION['user_id']);
$class = new course($dbpdo, $_GET['id']);
$class->get_owner();
$class->get_teachers();
$class->get_categories();
$class->get_attributes();

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

     $user->log_to_feed('updated class', $class->id);

     if($status == 0 && $old_status != 0)
       {
	 $tweet = "(" . date("U") . ") Unfortunately, " . $user->value . " has cancelled \"$name\".";
	 if(strlen($tweet) > 140)
	   $tweet = substr($tweet,0,135) . "...\".";
	 tweet($tweet);
	 $user->log_to_feed('canceled class', $class->id);
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
	   
	   tweet($config, $tweet);
	 }
     
     send_user_to("/class/" . $class->id . "/edit");
   }
}

?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>University of Reddit</title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="<?=PREFIX ?>/css/style.css">

  <script src="<?=PREFIX ?>/js/libs/modernizr-2.5.2.min.js"></script>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('../header.php');
  require_once('../social.php');

  ?>
  <div id="main" role="main">
    <div id="teach">
      <div class="content">
        <h1>Edit class</h1>
    <p>UReddit uses Markdown for formatting text, just like Reddit. <a href="http://old-wp.slekx.com/the-markdown-tutorial/" target="_blank">Here</a> is an online tutorial if you are unfamiliar with the syntax.</p>

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
<input type="text" name="url" class="teach" value="<?  try { echo post('url',$class->get_attribute_value('url')); } catch ( ObjectAttributeNotFoundException $e) {} ?>" />
<br /><br />

Course description:<br />
<textarea name="desc" class="teach"><? try { echo post('desc',$class->get_attribute_value('description')); } catch ( ObjectAttributeNotFoundException $e) {} ?></textarea>
<br /><br />

Syllabus:<br />
<textarea name="syllabus" class="teach"><? try { echo post('syllabus',$class->get_attribute_value('syllabus')); } catch ( ObjectAttributeNotFoundException $e) {} ?></textarea>
<br /><br />

Course prerequisites:<br />
<textarea name="prereq" class="teach"><? try { echo post('prereq',$class->get_attribute_value('prerequisites')); } catch ( ObjectAttributeNotFoundException $e) {} ?></textarea>
<br /><br />

Additional information:<br />
<textarea name="addinfo" class="teach"><? try { echo post('addinfo',$class->get_attribute_value('additional_information')); } catch ( ObjectAttributeNotFoundException $e) {} ?></textarea>
<br /><br />

Teacher qualifications:<br />
<textarea name="qualifications" class="teach"><? try { echo post('qualifications',$class->get_attribute_value('teacher_qualifications')); } catch ( ObjectAttributeNotFoundException $e) {} ?></textarea>
<br /><br />

Is this a live class?<br />
<select name="live">
<option <? try { echo $class->get_attribute_value('live') == "true" ? "SELECTED" : "" ; } catch ( ObjectAttributeNotFoundException $e) {}?> value="true">Yes</option>
<option <? try { echo $class->get_attribute_value('live') == "false" ? "SELECTED" : "" ; } catch ( ObjectAttributeNotFoundException $e) {}?> value="false">No</option>
</select><br /><br />

What is the status of your class?<br />
<select name="status">
  <? try { $status = $class->get_attribute_value('status'); } catch ( ObjectAttributeNotFoundException $e) {} ?>
  <option <? echo $status == "1" ? "SELECTED" : "" ?> value="1">has not begun, open for signups</option>
  <option <? echo $status == "2" ? "SELECTED" : "" ?> value="2">has not begun, closed to signups</option>
  <option <? echo $status == "3" ? "SELECTED" : "" ?> value="3">running and open for signups</option>
  <option <? echo $status == "4" ? "SELECTED" : "" ?> value="4">running but closed to signups</option>
  <option <? echo $status == "5" ? "SELECTED" : "" ?> value="5">completed</option>
  <option <? echo $status == "0" ? "SELECTED" : "" ?> value="0">cancelled</option>
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

        <h2>Tools</h2>
        <ul>
          <li>
            <a href="<?=PREFIX ?>/teachers">
              Teacher Admin Panel
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/edit">
              Edit Class Details
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/message">
              Mass Message
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/stats">
              Traffic Statistics
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('../footer.php'); ?>
</body>
</html>
