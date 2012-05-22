<?php  require_once('init.php'); ?>
   <div id="socialbar">
      <ul>
         <li>
            <div class="socialbar-item">
      <a href="http://twitter.com/uofreddit"><img src="<?=PREFIX ?>/img/twitter.png" alt="@uofreddit" id="twitter"></a><p class="content"><em>Latest <a href="http://twitter.com/uofreddit">Twitter</a> post</em>:<br>
      <?php
      $data = latest_tweet($dbpdo); echo '<a href="' . $data['url'] . '" class="nounderline">' . $data['text'] . '</a>';
      ?></p>
            </div>
         </li>
         <li>
            <div class="socialbar-item">
      <a href="http://reddit.com/r/UniversityofReddit"><img src="<?=PREFIX ?>/img/reddit.png" alt="/r/UniversityofReddit" id="reddit"></a><p class="content"><em>Top <a href="http://reddit.com/r/UniversityofReddit">Reddit</a> Post</em>:<br>
      <?php
      $data = latest_reddit_post($dbpdo); echo '<a href="' . $data['url'] . '" class="nounderline">' . $data['title'] . '</a>';
      ?></p>
            </div>
         </li>
         <li>
            <div class="socialbar-item">
      <a href="<?=PREFIX ?>/blog"><img src="<?=PREFIX ?>/img/wordpress.png" alt="UReddit Blog" id="wordpress"></a><p class="content"><em>Latest <a href="<?=PREFIX ?>/blog">WordPress</a> Post</em>:<br>
      <?php
	 $data = latest_blog_post($dbpdo); echo '<a href="' . PREFIX . $data['url'] . '" class="nounderline">' . $data['title'] . '</a>';
      ?></p>
            </div>
         </li>
         <li>
            <div class="socialbar-item">
      <a href="http://github.com/ureddit/ureddit"><img src="<?=PREFIX ?>/img/github.png" alt="ureddit on GitHub" id="github"></a><p class="content"><em>Latest <a href="http://github.com/ureddit/ureddit">GitHub</a> commit</em>:<br>
      <?php
      $data = latest_commit($dbpdo); echo '"<a href="' . $data['url'] . '" class="nounderline">' . $data['title'] . '</a>"';
      ?></p>
            </div>
         </li>
      </ul>
   </div>