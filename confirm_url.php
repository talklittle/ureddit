<?php

require_once('init.php');

$matches = array();
$fdata = stripslashes(file_get_contents($_POST['url']));
$pattern = '/noncollapsed.*http:\/\/www.reddit.com\/user\/([^"]*)".*md.*<p>(.*)<\/p>/i';
preg_match($pattern, $fdata, $matches);

if(count($matches) >= 2)
  {
    $reddit_username = $matches[1];
    $extracted_username = $matches[2];
    
    $user = new user($dbpdo, $_SESSION['user_id']);
    if($extracted_username == $user->value)
      {
	$user->define_attribute('reddit_username',$reddit_username,0);
	$user->save();
	echo "Success! You have been verified as Reddit user <strong>$reddit_username</strong>.";
      }
  }
else
    echo "There was a problem.";

?>