<?php

class UserNotFoundException extends Exception {}

class user extends object
{
  public $schedule = NULL;
  public $inbox = NULL;
  public $outbox = NULL;
  public $teaching = NULL;
  public $votes = NULL;

  function __construct($dbpdo, $id = NULL, $attribute_type = NULL)
  {
    parent::__construct($dbpdo, $id, $attribute_type);
  }

  function upvote($object_id)
  {
    $this->add_child($object_id, 'upvote', 0);
    $this->log_to_feed('upvoted class', $object_id);
  }

  function downvote($object_id)
  {
    $this->add_child($object_id, 'downvote', 0);
    $this->log_to_feed('downvoted class', $object_id);
  }

  function get_votes()
  {
    if($this->votes === NULL)
      {
	$this->votes = array();
	
	$this->get_children('class','upvote');
	$this->votes['upvoted'] = isset($this->children['class']) ? $this->children['class'] : array();
	
	$this->get_children('class','downvote');
	$this->votes['downvoted'] = isset($this->children['class']) ? $this->children['class'] : array();
      }
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

  function is_teacher()
  {
    if($this->teaching === NULL)
      $this->get_taught_classes();
    return !(count($this->teaching) == 0);
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
	  }
	return true;
      }
    else
      return false;
  }

  function get_inbox($offset, $limit)
  {
    $this->inbox = $this->dbpdo->query("SELECT a.id, aa.type, aa.value, a.creation, a.parent_id, aa.association_id FROM associations AS a INNER JOIN association_attributes AS aa ON aa.association_id = a.id AND a.child_id = ? AND (a.type = ? OR a.type = ? OR a.type = ? OR a.type = ?) AND (aa.type = ? OR aa.type = ?) ORDER BY a.creation DESC LIMIT $offset, $limit",
				       array(
					     $this->id,
					     'read_message',
					     'unread_message',
					     'read_mass_message',
					     'unread_mass_message',
					     'subject',
					     'body'
					     ));
  }

  function get_outbox($offset, $limit)
  {
    $this->outbox = $this->dbpdo->query("SELECT * FROM associations AS a INNER JOIN association_attributes AS aa ON aa.association_id = a.id AND a.parent_id = ? AND (a.type = ? OR a.type = ?) ORDER BY a.creation DESC LIMIT $offset, $limit",
					array(
					      $this->id,
					      'read_message',
					      'unread_message'
					      ));
  }

  function report_class($id)
  {
    $this->add_child($id, 'report', 0);
    $this->log_to_feed('reported class', $id);

  }

  function add_class($id)
  {
    $this->add_parent($id, 'enrolled_student', 0);
    if($this->config->memcache())
      $this->memcache_delete('v3_roster_' . $id . '_with_attribute_' . 'reddit_username');
    $this->log_to_feed('added class', $id);
    $this->upvote($id);
  }

  function drop_class($id)
  {
    $this->remove_parent($id, 'enrolled_student');
    if($this->config->memcache())
      $this->memcache_delete('v3_roster_' . $id . '_with_attribute_' . 'reddit_username');
    $this->log_to_feed('dropped class', $id);
  }

  function get_schedule()
  {
    if($this->schedule !== NULL)
      return;

    $this->get_parents('class','enrolled_student');
    if(isset($this->parents['class']))
      $this->schedule = $this->parents['class'];
    else
      $this->schedule = array();
  }

  function message($recepient_id, $subject, $message)
  {
    $association_id = $this->create_association($this->id, $recepient_id, 'unread_message', 0);
    $date = $this->timestamp();
    $this->dbpdo->query("INSERT INTO association_attributes (association_id, type,value,ring,creation,modification) VALUES (?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'subject',
			      $subject,
			      '0',
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

    $recepient = new user($this->dbpdo, $recepient_id);
    @send_email($this->value . '@ureddit.com', $recepient->value . '@ureddit.com', $subject, $this->process_text($message));
    $this->log_to_feed('PMed', $recepient_id);
  }
}

?>