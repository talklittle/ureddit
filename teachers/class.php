<?php
require_once('../init.php');

if((int)$_GET['id'] < 2000)
  {
    if($id = translate_class_id($dbpdo, $_GET['id']))
      send_user_to("/class/" . $id,"ureddit.com","301 Moved Permanently");
  }

try
  {
    $class = new course($dbpdo, $_GET['id']);
  }
catch (CourseNotFoundException $e)
  {
    send_user_to("/");
  }

$params['title'] .= ' : ' . $class->value;
require('../header2.php');

?>
    <div id="class-page">
      <div class="content">
        <?php
          $class->display_with_container(true, true);
        ?>
<div id="disqus_thread"></div>
<script type="text/javascript">
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    var disqus_shortname = 'ureddit'; // required: replace example with your forum shortname
    var disqus_identifier = 'ureddit-class-<?=$class->id ?>';
    var disqus_identifier = 'http://ureddit.com/class/<?=$class->id ?>/<?=$class->seo_string($class->value) ?>';
    var disqus_title = 'University of Reddit : <?=$class->value ?>';

/* * * DON'T EDIT BELOW THIS LINE * * */
(function() {
  var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
  dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
  (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
})();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
      </div>
    </div>
    <div id="teach-side">
      <div class="content">
        <?php
  include('tools.php');
        ?>
      </div>
    </div>
  <?php require_once('../footer2.php'); ?>
