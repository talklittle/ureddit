<?php

class link extends object
{
  public $title = NULL;
  public $url = NULL;

  function __construct($dbpdo, $id = NULL)
  {
    parent::__construct($dbpdo, $id);
    if($id !== NULL)
      {
	$this->get_title();
	$this->get_url();
      }
  }

  function set_title($title)
  {
    $this->define_attribute('title',$title,0);
    $this->title = $title;
    $this->save();
  }

  function get_title()
  {
    if($this->title === NULL)
      {
	try
	  {
	    $this->title = $this->get_attribute_value('title');
	  }
	catch (ObjectAttributeNotFoundException $e)
	  {
	    $this->title = NULL;
	  }
      }
    return $this->title;
  }

  function set_url($url)
  {
    $this->define_attribute('url',$url,0);
    $this->url = $url;
    $this->save();
  }

  function get_url()
  {
    if($this->url === NULL)
      {
	try
	  {
	    $this->url = $this->get_attribute_value('url');
	  }
	catch (ObjectAttributeNotFoundException $e)
	  {
	    $this->url = NULL;
	  }
      }
    return $this->url;
  }
}


?>