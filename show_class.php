<?php

require_once('init.php');

$class = new course($dbpdo, $_GET['id']);
$class->display($_GET['show'] == 'true' ? true : false);

?>