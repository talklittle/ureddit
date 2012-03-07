<?php

require('init.php');

if(!logged_in())
  send_user_to("/login");

?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>University of Reddit</title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="<?=PREFIX ?>/css/style.css">

  <script src="<?=PREFIX ?>/js/libs/modernizr-2.5.2.min.js"></script>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('header.php');
  require_once('social.php');
  ?>
  <div id="main" role="main">
    <div id="reddit-link">
      <div class="content">
      <h1>Link your Reddit account</h1>
      <p>
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
        <li>Go to <a href="http://redd.it/q3d3l">this post</a> in the /r/UniversityofReddit subreddit and log in as the account you wish to link to your UofR account.</li>
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
    </p>
    </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
