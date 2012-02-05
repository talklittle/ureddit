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

  function display($expand_categories = true, $filter = 'open', $expand_classes = false, $class_details = false)
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
      default:
	$show_statuses = array('1','3');
	break;
      }

    foreach($this->categories as $category_id)
      {
	$category = new category($this->dbpdo, $category_id);
	?><div class="category" id="category<?=$category->id ?>"><?php
	$category->display($expand_categories, $show_statuses, $expand_classes, $class_details);
	?></div><?php
      }
  }
}

?>