<?php

require_once('init.php');

if(!logged_in())
    send_user_to("/login.php");
?>
<!DOCTYPE html>
<html>
<head>
<?php include('favicon.html'); ?>
<meta charset=UTF-8>
<title>University of Reddit</title>
<link href="<?=PREFIX ?>/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jquery-1.4.2.min.js"></script>
</head>

<body>
<? require('header.php'); ?>
<div id="main">
  <div class="pagetitle">
  Link your account to Reddit
  </div>
  <?php
  try
    {
      $user = new user($dbpdo, $_SESSION['user_id']);
      $ru = $user->get_attribute_value('reddit_username');
      echo "You have been confirmed as reddit user <a href=\"http://reddit.com/user/" . $ru . "\">$ru</a>.";
    }
  catch (UserNotFoundException $e)
    {
      send_user_to("/");
    }
  catch (ObjectAttributeNotFoundException $e)
    {
      ?>
      In order to link your UniversityOfReddit.com account to your Reddit account, you must do the following:<br /><br />
      <ol style="list-style-type: decimal; padding-left: 30px;">
        <li>Go to <a href="http://redd.it/jwfzl">this post</a> in the /r/UniversityofReddit subreddit and log in as the account you wish to link to your UofR account.</li>
        <li>Post a top-level comment there containing your universityofreddit.com username <strong>and nothing else</strong>.</li>
        <li>Click on the "permalink" link underneath the comment you just posted; copy the URL into the textbox below:<br /><br />
          <input type="text" id="permalink" style="width: 400px;" /><br /><br />
        </li>
        <li>Press this:<br /><br />
          <button onclick="$('#response').html('[thinking...]');$.post('confirm_url.php',{url: $('#permalink').val()},function(data){$('#response').html(data);});" style="padding: 2px; margin-left: 15px;">Link my account!</button><br /><br />
        </li>
        <li><span id="response">[we'll see whether it works]</span></li>
      </ol>
      <?php
    }
  ?>
</div>

<?php require('footer.php'); ?>

</body>
</html>