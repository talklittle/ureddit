<?php

require_once('init.php');

//$key = "class" . $id;
//$memcache = new Memcache;
//$memcache->connect("localhost", 11211);
//$class = $memcache->get($key);
//if(!$class)
  //  {
$class = new course($dbpdo, $_GET['id']);
$class->display($_GET['show'] == 'true' ? true : false);
//  }
?>