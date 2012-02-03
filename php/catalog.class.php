<?php

class catalog extends object
{
  public $categories = NULL;

  function __construct($dbpdo)
  {
    parent::__construct($dbpdo, 1);
    $this->get_categories();
  }

  function get_categories()
  {
    $this->get_children('category','init');
    if(isset($this->children['category']))
      $this->categories = $this->children['category'];
    else
      $this->categories = array();
  }

  function display($expand_categories = true, $show_canceled_classes = false, $expand_classes = false, $class_details = false)
  {
    $categories = array();
    foreach($this->categories as $category_id)
      {
	$category = new category($this->dbpdo, $category_id);
	$this->category_objects[] = $category;

	?><div class="category" id="category<?=$category->id ?>"><?php
	$category->display($expand_categories, $show_canceled_classes, $expand_classes, $class_details);
	?></div><?php
      }
  }
}

?>