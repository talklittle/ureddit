<?php

require_once('init.php');

$user = new user($dbpdo, $dbpdo->session('user_id'));
$user->drop_class($_GET['id']);

signup_button($user,$_GET['id']);

?>