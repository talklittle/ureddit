<?php

class api extends base
{

  function log_api_request($type, $id)
  {
    $datetime = date("Y-m-d H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];

    $this->dbpdo->query("INSERT INTO `api_requests` (`datetime`,`type`,`id`,`ip`) VALUES (?, ?, ?, ?)", array(
												       $datetime,
												       $type,
												       $id,
												       $ip
												       ));
  }

  function __construct($dbpdo, $jsonp, $type, $id = NULL)
  {
    $this->dbpdo = $dbpdo;
    $response = array();

    $this->log_api_request($type, $id);

    if($type === NULL || strlen($type) == 0)
      {
	$response['error'] = 'Please choose an object about which you would like information. Options are: catalog, category, class, user, lecture, link. You must supply an ID for all types except catalog.';
      }

    switch($type)
      {
      case "catalog":
	$catalog = new catalog($this->dbpdo);
	$response['categories'] = $catalog->categories;
	break;
      case "category":
	if($id === NULL)
	  $response['error'] = 'Please provide the id of the category about which you would like information.';
	else
	  {
	    $category = new category($this->dbpdo, $id);
	    $response['name'] = $category->value;
	    $response['classes'] = $category->classes;
	  }
	break;
      case "class":
	if($id === NULL)
	  $response['error'] = 'Please provide the id of the class about which you would like information.';
	else
	  {
	    $class = new course($this->dbpdo, $id);
	    $response['name'] = $class->value;
	    $response['created'] = $class->created;
	    $response['last modified'] = $class->modified;

	    $status = $class->get_attribute_value('status');
	    $statuses = array(
			      '0' => 'canceled',
			      '1' => 'has not begun, open for signups',
			      '2' => 'has not begun, closed to signups',
			      '3' => 'running, open for signups',
			      '4' => 'running, closed to signups',
			      '5' => 'canceled'
			      );
	    $response['status'] = $status;
	    $response['status_description'] = $statuses[$status];
	    
	    $class->get_owner();
	    $response['owner'] = $class->owner;
	    
	    $class->get_teachers();
	    $response['teachers'] = $class->teachers;
	    
	    $response['description'] = $class->get_attribute_value('description');
	    $response['prerequisites'] = $class->get_attribute_value('prerequisites');
	    $response['syllabus'] = $class->get_attribute_value('syllabus');
	    $response['additional information'] = $class->get_attribute_value('additional_information');
	    $response['teacher qualifications'] = $class->get_attribute_value('teacher_qualifications');
	    
	    $class->get_lectures();
	    $response['lectures'] = $class->lectures;
	    //$response[''] = $class->get_attribute();
	    
	    $response['score'] = $class->calculate_score();
	    
	    $class->get_roster();
	    $response['roster'] = $class->roster;
	    
	  }
	break;
      case "user":
	if($id === NULL)
	  $response['error'] = 'Please provide the id of the category about which you would like information.';
	else
	  {
	    $user = new user($this->dbpdo, $id);
	    $response['username'] = $user->value;
	    $response['registered'] = $user->created;
	    
	    $user->get_schedule();
	    $response['schedule'] = $user->schedule;
	  }
	break;
      case "lecture":
	if($id === NULL)
	  $response['error'] = 'Please provide the id of the category about which you would like information.';
	else
	  {
	    $lecture = new lecture($this->dbpdo, $id);
	    $response['description'] = $lecture->description;
	    $response['links'] = $lecture->links;
	  }
	break;
      case "link":
	if($id === NULL)
	  $response['error'] = 'Please provide the id of the category about which you would like information.';
	else
	  {
	    $link = new link($this->dbpdo, $id);
	    $response['title'] = $link->title;
	    $response['url'] = $link->url;
	  }
	break;
      default:
	$response['error'] = 'The UReddit API does not support getting information about that object, or that object does not exist.';
	break;
      }

    echo $jsonp ? $jsonp . '(' . json_encode($response) . ');' : json_encode($response);
  }

}