<?php
require_once('init.php');

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
  <?php include('includes.php'); ?>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
<div id="fb-root"></div>
  <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
  <?php
  require_once('header.php');
  require_once('social.php');

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
    <div id="class-page-roster">
      <div class="content">
<div style="display: inline-block; position: relative; top: 3px;"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?=$class->url() ?>" data-text="UReddit: <?=$class->value ?>" data-via="uofreddit" data-count="none">Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>

<div style="display: inline-block; position: relative; top: 3px;"><div class="g-plusone" data-size="medium" data-annotation="none" data-href="http://reddit.com"></div><script type="text/javascript">(function() { var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true; po.src = 'https://apis.google.com/js/plusone.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s); })(); </script></div>

<div style="display: inline-block; margin-bottom: 1em;" class="fb-like" data-href="<?=$class->url() ?>" data-send="false" data-layout="button_count" data-width="40" data-show-faces="false" data-action="like"></div>
        <?php
          $class->display_roster();
        ?>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
