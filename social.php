<?php  require_once('init.php'); ?>
   <div id="socialbar">
      <ul>
         <li>
            <div class="socialbar-item">
      <a href="http://twitter.com/uofreddit"><img src="<?=PREFIX ?>/img/twitter.png" alt="@uofreddit" id="twitter"></a><p class="content"><em>Latest tweet</em>:<br> <?=latest_tweet($config); ?></p>
            </div>
         </li>
         <li>
            <div class="socialbar-item">
      <a href="http://reddit.com/r/UniversityofReddit"><img src="<?=PREFIX ?>/img/reddit.png" alt="/r/UniversityofReddit" id="reddit"></a><p class="content"><em>Latest Reddit Post</em>:<br>
      <?php
      $data = latest_reddit_post(); echo '<a href="' . $data['url'] . '" class="nounderline">' . $data['title'] . '</a>';
      ?></p>
            </div>
         </li>
         <li>
            <div class="socialbar-item">
               activity feed
            </div>
         </li>
         <li>
            <div class="socialbar-item">
               featured item
            </div>
         </li>
      </ul>
   </div>