<?php

class UserNotFoundException extends Exception {}

class user extends object
{
  public $schedule = NULL;
  public $inbox = NULL;
  public $outbox = NULL;
  public $teaching = NULL;

  function __construct($dbpdo, $id = NULL)
  {
    parent::__construct($dbpdo, $id);
  }

  function is_taking_class($id)
  {
    if($this->schedule === NULL)
      $this->get_schedule();
    if(in_array($id, $this->schedule))
      return true;
    return false;
  }

  function is_teaching_class($id)
  {
    if($this->teaching === NULL)
      $this->get_taught_classes();
    if(in_array($this->id, $this->teaching))
      return true;
    return false;
  }

  function hash_password($password)
  {
    return md5(md5($password) . "uofr!1336");
  }

  function get_taught_classes()
  {
    $this->get_children('class','teacher');
    if(isset($this->children['class']))
      $this->teaching = $this->children['class'];
    else
      $this->teaching = array();
  }

  function verify_credentials($username, $password)
  {
    $hash = $this->hash_password($password);
    $users = $this->dbpdo->query("SELECT objects.id FROM objects INNER JOIN object_attributes ON objects.value = ? AND object_attributes.type = 'password_hash' AND object_attributes.object_id = objects.id AND object_attributes.value = ?",
			   array(
				 $username,
				 $hash
				 ));
    if(count($users) > 0)
      {
	if($this->id === NULL)
	  {
	    $this->id = $users[0]['id'];
	    $this->lookup($this->id);
	    return true;
	  }
      }
    else
      return false;
  }

  function get_inbox($offset = NULL, $limit = NULL)
  {
    $this->get_parents('user','message', $offset, $limit);
    if($this->associations['message'] !== NULL)
      $this->inbox = $this->associations['message'];
    else
      $this->inbox = array();
  }

  function get_outbox($offset = NULL, $limit = NULL)
  {
    $this->get_children('user','message', $offset, $limit);
    if($this->associations['message'] !== NULL)
      $this->outbox = $this->associations['message'];
    else
      $this->outbox = array();
  }

  function report_class($id)
  {
    $this->add_child($id, 'report', 0);
  }

  function add_class($id)
  {
    $this->add_parent($id, 'enrolled_student', 0);
  }

  function drop_class($id)
  {
    $this->remove_parent($id, 'enrolled_student', 0);
  }

  function get_schedule()
  {
    $this->get_parents('class','enrolled_student');
    if(isset($this->parents['class']))
      $this->schedule = $this->parents['class'];
    else
      $this->schedule = array();
  }

  function message($recepient_id, $subject, $message)
  {
    $association_id = $this->create_association($this->id, $recepient_id, 'message', 0);
    $date = $this->timestamp();
    $this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES (?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'subject',
			      $subject,
			      0,
			      $date,
			      $date
			      ));

    $this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES (?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'body',
			      $message,
			      0,
			      $date,
			      $date
			      ));

    $this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES (?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'unread',
			      'true',
			      0,
			      $date,
			      $date
			      ));
  }

}

?>