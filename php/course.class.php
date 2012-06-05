<?php

class CourseNotFoundException extends Exception {}

class course extends object
{
  public $roster = NULL;
  public $teachers = NULL;
  public $reports = NULL;
  public $owner = NULL;
  public $categories = NULL;
  public $lectures = NULL;

  function __construct($dbpdo, $id = NULL)
  {
    parent::__construct($dbpdo, $id);
  }

  function get_owner()
  {
    $this->get_parents('user','owner');
    if(isset($this->parents['user']))
      $this->owner = $this->parents['user'][0];
    else
      $this->owner = array();
  }

  function assign_owner($id)
  {
    if($this->owner !== NULL)
      $this->remove_association($this->owner, $this->id, 'owner');
    $this->add_parent($id, 'owner', '0');
    $this->owner = $id;
  }

  function get_roster_with_attribute($attribute)
  {
    if(config::use_memcache)
      {
	$data = $this->memcache_get('v3_roster_' . $this->id . '_with_attribute_' . $attribute);
	if(!$data)
	  $data = $this->dbpdo->query("SELECT o.value, oa.value FROM (associations AS a INNER JOIN objects AS o ON a.parent_id = ? AND o.id = a.child_id AND a.type = ?) LEFT OUTER JOIN object_attributes AS oa ON o.id = oa.object_id AND oa.type = ?",
				      array(
					    $this->id,
					    'enrolled_student',
					    $attribute
					    ));
	$this->memcache_set('v3_roster_' . $this->id . '_with_attribute_' . $attribute, $data);
	return $data;
      }
    else
      {
	return $this->dbpdo->query("SELECT o.value, oa.value FROM (associations AS a INNER JOIN objects AS o ON a.parent_id = ? AND o.id = a.child_id AND a.type = ?) LEFT OUTER JOIN object_attributes AS oa ON o.id = oa.object_id AND oa.type = ?",
				   array(
					 $this->id,
					 'enrolled_student',
					 $attribute
					 ));
      }

  }

  function get_roster()
  {
    $this->get_children('user','enrolled_student');
    if(isset($this->children['user']))
      $this->roster = $this->children['user'];
    else
      $this->roster = array();
  }

  function get_lectures()
  {
    $this->get_children('lecture','component');
    if(isset($this->children['lecture']))
      $this->lectures = $this->children['lecture'];
    else
      $this->lectures = array();
  }

  function add_lecture($id)
  {
    $this->add_child($id, 'component', 0);
    $this->get_lectures();
  }

  function remove_lecture($id)
  {
    $this->remove_child($id,'component');
    $this->get_lectures();
  }

  function get_teachers()
  {
    $this->get_parents('user','teacher');
    if(isset($this->parents['user']))
      $this->teachers = $this->parents['user'];
    else
      $this->teachers = array();
  }

  function add_teacher($id)
  {
    if($this->teachers == NULL)
      {
	$this->add_parent($id, 'teacher', 0);
	$this->teachers = array($id);
      }
    elseif(is_array($this->teachers) && !in_array($id, $this->teachers))
      {
	$this->add_parent($id, 'teacher', 0);
	$this->teachers[] = $id;
      }
  }

  function remove_teacher($id)
  {
    $this->remove_parent($id,'teacher');
    $this->get_teachers();
  }

  function get_reports()
  {
    $this->get_parents('user','report');
    if(isset($this->parents['user']))
      $this->reports = $this->parents['user'];
    else
      $this->reports = array();
  }

  function get_categories()
  {
    $this->get_parents('category', 'categorization');
    if(isset($this->parents['category']))
      $this->categories = $this->parents['category'];
    else
      $this->categories = array();
  }

  function add_to_category($id)
  {
    if($this->categories == NULL)
      $this->get_categories();
    if(!in_array($id, $this->categories))
      {
	$this->add_parent($id, 'categorization', 0);
	$this->categories[] = $id;
      }
  }

  function remove_from_category($id)
  {
    $this->remove_parent($id, 'categorization');
    $this->get_categories();
  }

  function mass_message($subject, $message, $author)
  {
    if($this->roster === NULL)
      $this->get_roster();

    try
      {
	$author = new user($this->dbpdo, $this->session('user_id'));
      }
    catch(ObjectNotFoundException $e)
      {
	return;
      }

    foreach($this->roster as $user_id)
      {
	$association_id = $this->create_association($this->id, $user_id, 'unread_mass_message', 0);
	$date = $this->timestamp();
	$this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES (?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'subject',
			      $subject,
			      0,
			      $date,
			      $date
			      ));

	$this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES (?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'body',
			      $message,
			      0,
			      $date,
			      $date
			      ));

	$this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES (?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'author',
			      $author->id,
			      0,
			      $date,
			      $date
			      ));
      }

    $author->log_to_feed('mass messaged the students in', $this->id);
  }

  function calculate_score()
  {
    $votes = $this->get_parents('user','upvote');
    $score = isset($this->parents['user']) ? count($this->parents['user']) : 0;
    $votes = $this->get_parents('user','downvote');
    $score -= isset($this->parents['user']) ? count($this->parents['user']) : 0;
    return $score;
  }

  function display_full()
  {

  }

  function display_roster()
  {
    ?>
	      <div class="class-name">
	        Roster
	      </div>
	      <div class="class-desc">
	      <?php 

	      $data = $this->get_roster_with_attribute('reddit_username');
	      $count = 0;
	      foreach($data as $user)
		{
		  echo ++$count . '. <a href="' . PREFIX . '/user/' . $user[0] . '" style="color: black;">' . $user[0] . '</a>';
		  if(isset($user[1]))
		    {
		      echo ' <a href="http://www.reddit.com/message/compose/?to=' . $user[1] . '"><img src="' . SRVDOMAIN . PREFIX . '/img/reddit-small.png" style="border: 0; height: 1em;" /></a>';
		    }
		  echo '<br />';
		}
	      if($count == 0)
		{
		  echo "<em>no students found</em>";
		}
	      ?></div><?php
  }

  function display_with_container($expanded = false, $full = false)
  {
    ?>
    <div class="class" id="class<?=$this->id ?>">
       <div class="content">
    <?php
    $this->display($expanded, $full);
    ?>
       </div>
    </div>
    <?php
  }

  function display_without_container($expanded = false, $full = false)
  {
    $this->display($expanded, $full);
  }

  function display_lectures($editing = false)
  {
    ?>
<ol><?php

$count = 0;

$this->get_lectures();
foreach($this->lectures as $lecture_id)
  {
    $lecture = new lecture($this->dbpdo, $lecture_id);
    echo '<li><i>' . $lecture->value . '</i>';
    if($editing)
      echo ' <small>[<a href="' . PREFIX . '/teachers/edit_lecture.php?id=' . $lecture_id . '">edit</a>] [<a href="' . PREFIX . '/teachers/delete_lecture.php?id=' . $lecture_id . '">delete</a>]</small>';
    echo "<br>";
    echo '<p>';
    echo $this->process_text($lecture->description);
    echo '</p>';


    $lecture->get_links();
    echo '<ul>';
    foreach($lecture->links as $link_id)
      {
	$link = new link($lecture->dbpdo, $link_id);
	echo '<li><a href="' . $link->url . '" target="_blank">' . $link->title . '</a>';
	if($editing)
	  echo ' <small>[<a href="' . PREFIX . '/teachers/delete_link.php?lecture=' . $lecture->id . '&link=' . $link->id . '">delete</a>]</small>';
	echo '</li>';
      }
    if($editing)
      {
    ?>
    <li>
       <form method="post" action="<?=PREFIX ?>/teachers/new_link.php">
       New link title: <input type="text" name="title" id="title"><br>
       New link URL: <input type="text" name="url" id="url"><br>
       <input type="hidden" name="lecture" id="lecture" value="<?=$lecture->id ?>">
       <input type="submit"></form>
    </li>
    <?php
	  $last = $lecture->order;
       }
    echo '</ul>';
  }

if($editing)
  {
?>

<li>
<strong>New Lecture</strong>
<form method="post" action="<?=PREFIX ?>/class/<?=$this->id ?>/lectures">
  <p>
  Lecture Title:<br>
  <input type="text" name="title" id="title" class="teach" value="<?=post('title','') ?>">
  </p>

  <p>
  Lecture Description:<br>
  <textarea name="desc" id="desc" class="teach"><?=post('desc','') ?></textarea>
  </p>

<input type="hidden" name="order" value="<?=isset($last) ? $last : '0' ?>">
<input type="submit" value="Add">
</form>
</li>
</ol>
   <?php
    }
  }

  function display($expanded = false, $full = false)
  {
    $comments = array();
    if($expanded)
      $comments[] = 'expanded';
    if($full)
      $comments[] = 'full';
    //$this->log_user_view(implode(';',$comments));

    if($this->session('user_id') !== false)
      {
	$user = new user($this->dbpdo, $this->session('user_id'));
	$user->get_votes();
      }
    else
      $user = $this->dbpdo;

    ?>
          <div class="voting">
             <?=votebox($this, $this->session('logged_in') ? $user : false) ?>
          </div>
	  <?php
	     if(!$full)
	       {
		 ?>
                   <div class="showhide">
	           [<a
		   onclick="$.get('<?=PREFIX ?>/show_class.php',{id: '<?=$this->id ?>', show: '<?=$expanded == 'true' ? 'false' : 'true' ?>'}, function(data){$('#class<?=$this->id ?> > .content').html(data);});"
		   ><?=($expanded == true ? "-" : "+") ?></a>]
		   </div> 
		 <?php
	       }

    signup_button($user,$this->id);
          ?>
          <div class="class-name<?=($expanded == true ? ' expanded' : '') ?>">
        <?php
        echo htmlspecialchars(stripslashes($this->value));
        if(date("U") - strtotime($this->created) < 60*60*24*7)
          echo ' <span style="color: red;">(new!)</span>';
        try
	  {
	    /*
	    if($this->get_attribute_value('live') == 'true')
	      {
		?>
		<img src="<?=PREFIX ?>/images/live.png" alt="live class!" class="live"  />
	        <span style="font-style: italic; font-weight: normal; font-size: 0.8em;">
		  live lectures!
                </span>
		<?php
	      }
	    */
	    if($this->teachers === NULL)
	      $this->get_teachers();
	    if($this->owner === NULL)
	      $this->get_owner();
	    if(in_array($this->session('user_id'), $this->teachers))
	      {
		?>
		<span style="font-weight: normal; font-size: 0.8em;">
		  [&nbsp;<a href="<?=PREFIX ?>/class/<?=$this->id ?>/admin">edit class</a>&nbsp;]
		</span>
		<?php
	      }
	    ?><br /><?php
	  }
	catch (ObjectAttributeNotFoundException $e)
	  {

	  }
        ?>
        </div>
        <?php 

        if($expanded == true)
	  {
            ?>
            <div class="class-desc-nounderline">
              <?php
	      try
	        {
		  echo process(stripslashes($this->get_attribute_value('description')));
		}
  	      catch (ObjectAttributeNotFoundException $e)
		{
		  echo "<em>no description</em>";
		}
	      ?>
            </div>
	    <div class="class-info">
            <?php
            try
              {
		$teachers = array();
		foreach($this->teachers as $teach)
		  {
		    $user = new user($this->dbpdo, $teach);
		    if(strlen($user->value) == 0)
		      throw new ObjectNotFoundException;
		    $text = "<a href=\"" . PREFIX  . "/user/" . $user->value . "\" class=\"link-class-desc\">" . $user->value . "</a>";
		    try
		      {
			$ru = $user->get_attribute_value('reddit_username');
			$text .= "<a href=\"http://reddit.com/user/$ru\"><img style=\"border: 0; width: 1em; height: 1em; margin: 0 3px;\" src=\"" . SRVDOMAIN . PREFIX . "/img/reddit-small.png\"></a>";
		      }
		    catch (ObjectAttributeNotFoundException $e)
		      {

		      }
		    $teachers[] = $text;
		  }
		    
		echo ' <button class="button">by ' . implode($teachers, ", ") . '</button> ';
              }
	    catch (ObjectNotFoundException $e)
	      {
	      echo 'error: teacher not found ';
	      }
	    catch (ObjectAttributeNotFoundException $e)
	      {

	      }

	    try
	      {
                echo "<a href=\"" . htmlspecialchars(stripslashes($this->get_attribute_value('url'))) . "\"><button class=\"button\">class URL</button></a> ";
	      }
   	    catch (ObjectAttributeNotFoundException $e)
              {
	      }
            if(exec("ls files | grep class" . $this->id))
              echo "<a href=\"/class/" . $this->id . "/files\"><button class=\"button\">class files</button></a> ";

            ?>
            <a href="<?=PREFIX ?>/class/<?=$this->id ?>/<?=$this->seo_string($this->value) ?>"><button class="button">class page</button></a>
            <?php
	    if(logged_in() && $full)
	      {
		?>
		<span id="report<?=$this->id ?>"><a style="text-decoration: underline; cursor: pointer;" onclick="$.post('<?=PREFIX ?>/report.php',{class: <?=$this->id ?>},function(response){$('#report<?=$this->id ?>').html(response); return false;});"><button class="button">report class</button></a></span>
		<?php
	      }
	    ?>
	    </div>
	    <?php
	  } 
        ?>
        <?php
	if($full)
	  {
	    ?>
              <div class="class-name expanded">
                Lectures
	      </div>
	      <div class="class-desc">
	      <?php
	      $this->display_lectures();

	      ?>
	      </div>

              <div class="class-name expanded">
                Prerequisites
	      </div>
	      <div class="class-desc">
	      <?php
	      try
	        {
		  echo process(stripslashes($this->get_attribute_value('prerequisites')));
		}
	      catch (ObjectAttributeNotFoundException $e)
		{
		  echo "<em>none</em>";
		}

	      ?>
	      </div>

	      <div class="class-name expanded">
	        Syllabus
	      </div>
	      <div class="class-desc">
	      <?php
	      try
		{
		  echo process(stripslashes($this->get_attribute_value('syllabus')));
		}
	      catch (ObjectAttributeNotFoundException $e)
		{
		  echo "<em>none</em>";
		}
	      ?>
	      </div>

	      <div class="class-name expanded">
	        Additional information
	      </div>
	      <div class="class-desc">
	      <?php
	      try
		{
		  echo process(stripslashes($this->get_attribute_value('additional_information')));
		}
	      catch (ObjectAttributeNotFoundException $e)
		{
		  echo "<em>none</em>";
		}
	      ?>
	      </div>

	      <div class="class-name expanded">
		Teacher qualifications
	      </div>
	      <div class="class-desc">
	      <?php
	      try
		{
		  echo process(stripslashes($this->get_attribute_value('teacher_qualifications')));
		}
	      catch (ObjectAttributeNotFoundException $e)
		{
		  echo "<em>not given</em>";
		}
	      ?>
	      </div>

	    <?php
	    }
	    ?>
    <?php
  }

}

?>