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
	$response['categories'] = array();
	foreach($catalog->categories as $category_id)
	  {
	    $category = new category($this->dbpdo, $category_id);
	    $category_info = array();
	    $category_info['id'] = $category->id;
	    $category_info['value'] = $category->value;
	    $response['categories'][] = $category_info;
	  }
	break;
      case "category":
	if($id === NULL)
	  $response['error'] = 'Please provide the id of the category about which you would like information.';
	else
	  {
	    $category = new category($this->dbpdo, $id);
	    $response['name'] = $category->value;
	    $response['classes'] = array();
	    foreach($category->classes as $class_id)
	      {
		$course = new course($this->dbpdo, $class_id);
		$course_info = array();
		$course_info['id'] = $course->id;
		$course_info['value'] = $course->value;
		$response['classes'][] = $course_info;
	      }

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
	    $owner = new user($this->dbpdo, $class->owner);
	    $owner_info = array();
	    $owner_info['id'] = $owner->id;
	    $owner_info['value'] = $owner->value;
	    $response['owner'] = $owner_info;
	    
	    $class->get_teachers();
	    $response['teachers'] = array();
	    foreach($class->teachers as $user_id)
	      {
	      	$teacher = new user($this->dbpdo, $user_id);
		$teacher_info = array();
		$teacher_info['id'] = $teacher->id;
		$teacher_info['value'] = $teacher->value;
		$response['teachers'][] = $teacher_info;
	      }
	    
	    $response['description'] = $class->get_attribute_value('description');
	    $response['prerequisites'] = $class->get_attribute_value_or_null('prerequisites');
	    $response['syllabus'] = $class->get_attribute_value_or_null('syllabus');
	    $response['additional information'] = $class->get_attribute_value_or_null('additional_information');
	    $response['teacher qualifications'] = $class->get_attribute_value_or_null('teacher_qualifications');
	    
	    $class->get_lectures();
	    $response['lectures'] = array();
	    foreach($class->lectures as $lecture_id)
	      {
		$lecture = new lecture($this->dbpdo, $lecture_id);
		$lecture_info = array();
		$lecture_info['id'] = $lecture->id;
		$lecture_info['value'] = $lecture->value;
		$response['lectures'][] = $lecture_info;
	      }
	    
	    $response['score'] = $class->calculate_score();
	    
	    $class->get_roster();
	    $response['roster'] = array();
	    foreach($class->roster as $user_id)
	      {
	      	$user = new user($this->dbpdo, $user_id);
		$user_info = array();
		$user_info['id'] = $user->id;
		$user_info['value'] = $user->value;
		$response['roster'][] = $user_info;
	      }
	   
	    $viewer_id = $this->session('logged_in') ? $this->session('user_id') : NULL;
	    if ($viewer_id !== NULL)
	      {
	      	$viewer = new user($this->dbpdo, $viewer_id);
		$response['enrolled'] = $viewer->is_taking_class($class->id);
		
		$viewer->get_votes();
		if (in_array($class->id, $viewer->votes['upvoted']))
		  $response['likes'] = true;
		elseif (in_array($class->id, $viewer->votes['downvoted']))
		  $response['likes'] = false;
		else
		  $response['likes'] = NULL;
	      }
	    else
	      {
		$response['enrolled'] = NULL;
		$response['likes'] = NULL;
	      }
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
	    $response['schedule'] = array();
	    foreach($user->schedule as $class_id)
	      {
		$course = new course($this->dbpdo, $class_id);
		$course_info = array();
		$course_info['id'] = $course->id;
		$course_info['value'] = $course->value;
		$response['schedule'][] = $course_info;
	      }

	  }
	break;
      case "lecture":
	if($id === NULL)
	  $response['error'] = 'Please provide the id of the category about which you would like information.';
	else
	  {
	    $lecture = new lecture($this->dbpdo, $id);
	    $response['description'] = $lecture->description;
	    $response['links'] = array();
	    foreach($lecture->links as $link_id)
	      {
		$link = new link($this->dbpdo, $class_id);
		$link_info = array();
		$link_info['id'] = $link->id;
		$link_info['value'] = $link->value;
		$response['links'][] = $link_info;
	      }
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