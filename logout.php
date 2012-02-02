<?php
require_once('init.php');
session_start();

$dbpdo->query("DELETE FROM `sessions` WHERE `object_id` = ?", array($_SESSION['user_id']));
setcookie('ureddit_sessid',"",time()-60*60*24);

$_SESSION = array();
session_destroy();
send_user_to("/");
?>