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

?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>University of Reddit : <?=$class->value ?></title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <?php include('../includes.php'); ?>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('../header.php');
  require_once('../social.php');

  if(isset($_GET['category_id']) && count($dbpdo->query("SELECT `id` FROM `objects` WHERE `id` = ? AND `type` = 'category' LIMIT 1", array($_GET['category_id'])) != 0))
    $active_category_id = $_GET['category_id'];
  else
    $active_category_id = -1;

  $catalog = new catalog($dbpdo);
  ?>
  <div id="main" role="main">
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
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('../footer.php'); ?>
</body>
</html>
