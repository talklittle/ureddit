<?php

require_once('init.php');

$category = new category($dbpdo, $_GET['id']);
$category->display($_GET['show'] == 'false' ? false : true);

?>