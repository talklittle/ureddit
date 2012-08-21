<?php

class category extends object
{
  public $classes = NULL;

  function __construct($dbpdo, $id = NULL)
  {
    parent::__construct($dbpdo, $id);
    if($id !== NULL)
      $this->get_classes();
  }

  function get_classes()
  {
    $this->get_children('class','categorization');
    if(isset($this->children['class']))
      {
	$this->classes_api = $this->children['class'];
	$this->classes = array();
	foreach($this->classes_api as $clazz)
	  {
	    $this->classes[] = $clazz['id'];
	  }
      }
    else
      $this->classes = array();
  }

  function display($expand_category = true, $filter = 'all', $expand_classes = false, $class_details = false)
  {
    switch($filter)
      {
      case 'open':
	$show_statuses = array('1','3');
	break;
      case 'closed':
	$show_statuses = array('2','4');
	break;
      case 'completed':
	$show_statuses = array('5');
	break;
      case 'all':
	$show_statuses = array('1','2','3','4','5');
	break;
      default:
	$show_statuses = array('1','3');
	break;
      }
    ?>
    <div class="category <?=isset($_GET['category_id']) && $_GET['category_id'] == $this->id ? ' active' : '' ?>">
    <div class="content">
      <?php
      if($expand_category)
	{
	  echo $this->value;
	  /*
	  ?>
	  <span class="showhide"><a onclick="$.get('<?=PREFIX ?>/category.php',{id: '<?=$this->id ?>', show: 'false', filter: '<?=$filter ?>'}, function(data) { $('#category<?=$this->id ?>').html(data)});" class="link-showhide">[hide]</a></span></div>
	  <?php
	  */
	  $found = false;
	  foreach($this->classes as $class_id)
	    {
	      $class = new course($this->dbpdo, $class_id);
	      try
	        {
		  if(in_array($class->get_attribute_value('status'), $show_statuses))
		    {
		      $class->display_with_container($expand_classes, $class_details);
		      $found = true;
		    }
		}
	      catch (ObjectAttributeNotFoundException $e)
	      {
		//$class->display($expand_classes, $class_details);
	      }
	    }
	  if(!$found)
	    {
	      echo '<p style="font-weight: normal; font-style: italic; font-size: 0.8em; padding-left: 1em;">No classes found. <a href="' . PREFIX . '/teach">Why not start one?</a></p>';
	    }
	  echo '</div></div>';
	}
      else
	{
	  /*
	  ?>
	  <span class="showhide"><a onclick="$.get('<?=PREFIX ?>/category.php',{id: '<?=$this->id ?>', show: 'true', filter: '<?=$filter ?>'}, function(data) { $('#category<?=$this->id ?>').html(data)});" class="link-showhide">[show]</a></span></div>
	  <?php
	  */
	  echo '<a href="' . PREFIX . ($filter == 'completed' ? '/archive' : '') . '/category/' . $this->id . '/' . $this->seo_string($this->value) . '">' . $this->value . '</a>';
	  echo '</div></div>';
	}
  }
}

?>