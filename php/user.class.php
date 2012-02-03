<?php

class UserNotFoundException extends Exception {}

class user extends object
{
  public $schedule = NULL;
  public $inbox = NULL;
  public $outbox = NULL;

  function __construct($dbpdo, $id = NULL)
  {
    parent::__construct($dbpdo, $id);
  }

  function get_inbox()
  {
    $this->get_parents('user','message');
    $this->inbox = $this->associations['message'];
  }

  function get_outbox()
  {
    $this->get_children('user','message');
    $this->outbox = $this->associations['message'];
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
    $this->schedule = $this->parents['class'];
  }

  function message($receipient_id, $subject, $message)
  {
    $association_id = $this->create_association($this->id, $recepient_id, 'message', 0);
    $date = $this->timestamp();
    $this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES ?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'subject',
			      $subject,
			      0,
			      $date,
			      $date
			      ));

    $this->dbpdo->query("INSERT INTO `association_attributes` (`association_id`, `type`,`value`,`ring`,`creation`,`modification`) VALUES ?, ?, ?, ?, ?, ?)",
			array(
			      $association_id,
			      'body',
			      $message,
			      0,
			      $date,
			      $date
			      ));
  }

}

?>