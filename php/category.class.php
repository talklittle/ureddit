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
      $this->classes = $this->children['class'];
    else
      $this->classes = array();
  }

  function display($expand_category = true, $filter = 'open', $expand_classes = false, $class_details = false)
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
    <div class="category <?=$_GET['category_id'] == $this->id ? ' active' : '' ?>">
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
	  foreach($this->classes as $class_id)
	    {
	      $class = new course($this->dbpdo, $class_id);
	      try
	        {
		  if(in_array($class->get_attribute_value('status'), $show_statuses))
		    $class->display($expand_classes, $class_details);
		}
	      catch (ObjectAttributeNotFoundException $e)
	      {
		//$class->display($expand_classes, $class_details);
	      }
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
	  echo '<a href="' . PREFIX . '/category/' . $this->id . '/' . $this->seo_string($this->value) . '">' . $this->value . '</a>';
	  echo '</div></div>';
	}
  }
}

?>