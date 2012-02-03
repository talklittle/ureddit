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

  function display($expand_classes = false, $class_details = false)
  {
    foreach($this->classes as $class_id)
      {
	$class = new course($this->dbpdo, $class_id);
	$class->display($expand_classes, $class_details);
      }
  }
}

?>