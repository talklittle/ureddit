<?php

require('init.php');

if(!logged_in())
  send_user_to("/login");

$params['title'] .= ' : Link Reddit Account';
require('header2.php');
?>

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
        <li>Go to <a href="http://redd.it/ym40s">this post</a> in the /r/UniversityofReddit subreddit and log in as the account you wish to link to your UofR account.</li>
        <li>Post a top-level comment there containing your universityofreddit.com username <strong>and nothing else</strong>.</li>
        <li>Click on the "permalink" link underneath the comment you just posted; copy the URL into the textbox below:<br /><br />
          <input type="text" id="permalink" style="width: 400px;" /><br /><br />
        </li>
        <li>Press this:<br /><br />
          <button onclick="$('#response').html('[thinking...]');$.post('confirm_url.php',{url: $('#permalink').val()},function(data){$('#response').html(data);});" style="padding: 2px; margin-left: 15px;">Link my account!</button><br /><br />
        </li>
        <li><span id="response">[we will soon find out whether it works]</span></li>
      </ol>
      <?php
    }

    ?>
    </p>
    </div>
    </div>
<?php require_once('footer2.php'); ?>
