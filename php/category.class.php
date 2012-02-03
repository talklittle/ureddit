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

  function display($expand_category = true, $expand_classes = false, $class_details = false)
  {
    ?>
    <div class="category-name">
      <?=$this->value; ?>
      <?php
      if($expand_category)
	{
	  ?>
	  <span class="showhide"><a onclick="$.get('<?=PREFIX ?>/category.php',{id: '<?=$this->id ?>', show: 'false' }, function(data) { $('#category<?=$this->id ?>').html(data)});" class="link-showhide">[hide]</a></span></div>
	  <?php
	  foreach($this->classes as $class_id)
	    {
	      $class = new course($this->dbpdo, $class_id);
	      $class->display($expand_classes, $class_details);
	    }
	}
      else
	{
	  ?>
	  <span class="showhide"><a onclick="$.get('<?=PREFIX ?>/category.php',{id: '<?=$this->id ?>', show: 'true'}, function(data) { $('#category<?=$this->id ?>').html(data)});" class="link-showhide">[show]</a></span></div>
	  <?php
	}
  }
}

?>