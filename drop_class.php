<?php

require_once('init.php');

$user = new user($dbpdo, $_GET['id']);
$user->drop_class($_GET['id']);

signup_button($_GET['id']);

?>