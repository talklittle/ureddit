<?php

//class UserNotFoundException extends Exception {}

class lecture extends object
{
  public $class_id = NULL;
  public $links = NULL;
  public $order = NULL;
  public $description = NULL;

  function __construct($dbpdo, $id = NULL)
  {
    parent::__construct($dbpdo, $id);
    if($id !== NULL)
      {
	$this->get_class_id();
	$this->get_links();
	$this->get_description();
	$this->get_order();
      }
  }

  function get_class_id()
  {
    if($this->id === NULL)
      return;
    $this->get_parents('class','component');
    if(isset($this->parents['class']))
      $this->class_id = $this->parents['class'][0];
    return $this->class_id;
  }

  function get_links()
  {
    $this->get_children('link','detail');
    if(isset($this->children['link']))
      $this->links = $this->children['link'];
    else
      $this->links = array();
    return $this->links;
  }

  function add_link($title, $url)
  {
    $link = new link($this->dbpdo);
    $link->define('link',$title,0);
    $link->save();

    $link->define_attribute('url',$url,0);
    $link->define_attribute('title',$title,0);
    $link->save();

    $this->add_child($link->id,'detail',0);
    $this->get_links();
  }

  function remove_link($id)
  {
    $this->remove_child($id);
  }

  function set_description($desc)
  {
    $this->define_attribute('description', $desc, 0);
    $this->description = $desc;
    $this->save();
  }

  function get_description()
  {
    if($this->description === NULL)
      {
	try
	  {
	    $this->description = $this->get_attribute_value('description');
	  }
	catch (ObjectAttributeNotFoundException $e)
	  {
	    $this->description = NULL;
	  }
      }
    return $this->description;
  }

  function set_order($order)
  {
    $this->define_attribute('order', $order, 0);
    $this->order = $order;
    $this->save();
  }

  function get_order()
  {
    if($this->order === NULL)
      try
	{
	  $this->order = $this->get_attribute_value('order');
	}
    catch (ObjectAttributeNotFoundException $e)
      {
	$this->order = NULL;
      }
    return $this->order;
  }

  function switch_order_with($id)
  {
    $lecture = new lecture($this->dbpdo, $id);
    $neworder = $lecture->order;
    $lecture->set_order($this->order);
    $this->set_order($neworder);
  }

}