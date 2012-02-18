<?php

require_once('init.php');

$class = new course($dbpdo, $_GET['id']);
$class->display_without_container($_GET['show'] == 'true' ? true : false);

?>