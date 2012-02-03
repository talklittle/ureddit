<?php

class CourseNotFoundException extends Exception {}

class course extends object
{
  public $roster = NULL;
  public $teachers = NULL;
  public $reports = NULL;
  public $owner = NULL;
  public $categories = NULL;

  function __construct($dbpdo, $id = NULL)
  {
    parent::__construct($dbpdo, $id);
    $this->get_owner();
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

  function get_roster()
  {
    $this->get_children('user','enrolled_student');
    if(isset($this->children['user']))
      $this->roster = $this->children['user'];
    else
      $this->roster = array();
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
    elseif(is_array($this->teachers) && !in_array($this->teachers, $id))
      {
	$this->add_parent($id, 'teacher', 0);
	$this->teachers[] = $id;
      }
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
    if(!in_array($this->categories, $id))
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
    foreach($this->roster as $user_id)
      {
	$association_id = $this->create_association($this->id, $user_id, 'mass_message', 0);
	$date = $this->timestamp();
	$this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES ?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'subject',
			      $subject,
			      0,
			      $date,
			      $date
			      ));

	$this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES ?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'body',
			      $message,
			      0,
			      $date,
			      $date
			      ));

	$this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES ?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'author',
			      $author->id,
			      0,
			      $date,
			      $date
			      ));
      }
  }

  function display($expanded = false, $full = false)
  {
    ?>
    <div id="class<?=$this->id ?>">
      <div class="class">
        <?php
        if(!$full)
          {
	    ?>
            <div style="font-size: 0.8em; font-weight:bold; float:left; padding-right: 8px;">
              [<a
                style="cursor: pointer;"
                onclick="$.get('<?=PREFIX ?>/show_class.php',{id: '<?=$this->id ?>', show: '<?=$expanded == 'true' ? 'false' : 'true' ?>'}, function(data){$('#class<?=$this->id ?>').html(data);});"
              ><?=($expanded == true ? "-" : "+") ?></a>]
            </div> 
            <?php
            //signup_button($this->id)
	  }
        ?>
      <div class="class-name">
        <?php
        echo htmlspecialchars(stripslashes($this->value));

        try
	  {
	    if($this->get_attribute_value('live') == 'true')
	      {
		?>
		<img src="<?=PREFIX ?>/images/live.png" alt="live class!" style="height: 0.8em; margin-left: 3px;" />
	        <span style="font-style: italic; font-weight: normal; font-size: 0.8em;">
		  live lectures!
                </span>
		<?php
		  if($this->teachers === NULL)
		    $this->get_teachers();
		  if(in_array($_SESSION['user_id'], $this->teachers))
		    {
		      ?>
		      <span style="font-weight: normal; font-size: 0.8em;">
			[ <a href="<?=PREFIX ?>/class/<?=$this->id ?>/edit">edit</a> ]
			[ <a href="<?=PREFIX ?>/class/<?=$this->id ?>/message">mass message</a> ]
		      </span>
			<?php
		    }
		?><br /><?php
              }
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
            <div class="class-desc">
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
                $teacher = new user($this->dbpdo, $this->owner);
                ?>taught by <a href="<?=PREFIX ?>/user/<?=$teacher->value ?>" class="link-class-desc"><?=$teacher->value ?></a>
		<?php
		$ru = $teacher->get_attribute_value('reddit_username');
		?>
                    [<a href="http://reddit.com/user/<?=$ru ?>" class="link-class-desc">teacher reddit user page</a>]
                    <?php
              }
	    catch (UserNotFoundException $e)
	      {
		
	      }
	    catch (ObjectAttributeNotFoundException $e)
	      {

	      }

	    try
	      {
                echo "[<a href=\"" . htmlspecialchars(stripslashes($this->get_attribute_value('url'))) . "\" class=\"link-class-desc\">class URL</a>] ";
	      }
   	    catch (ObjectAttributeNotFoundException $e)
              {
	      }
            if(exec("ls files | grep class" . $this->id))
              echo "[<a href=\"/class/" . $this->id . "/files\" class=\"link-class-desc\">class files</a>] ";

            ?>
            [<a href="<?=PREFIX ?>/class/<?=$this->id ?>" class="link-class-desc">class page</a>]
            <?php
	    if(logged_in())
	      {
		?>
		<span id="report<?=$this->id ?>">[<a href="#" class="link-class-desc" onclick="$.post('<?=PREFIX ?>/report.php',{class: <?=$this->id ?>},function(response){$('#report<?=$this->id ?>').html(response); return false;});">report class]</a></span>
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
	      <br /><br />
              <div class="class-name">
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
	      <br /><br />

	      <div class="class-name">
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
	      <br /><br />

	      <div class="class-name">
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
	      <br /><br />

	      <div class="class-name">
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
	      <br /><br />

	      <div class="class-name">
	        Roster
	      </div>
	      <div class="class-desc">
	      <?php
	      $this->get_roster();
	      $count = 0;
	      foreach($this->roster as $user_id)
		{
		  $user = new user($this->dbpdo, $user_id);
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
	    <?php
	    }
	    ?>
	  </div>
	</div>
    <?php
  }

}

?>