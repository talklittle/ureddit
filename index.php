<?php
require_once('init.php');
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

  if(isset($_GET['category_id']) && count($dbpdo->query("SELECT `id` FROM `objects` WHERE `id` = ? AND `type` = 'category' LIMIT 1", array($_GET['category_id'])) != 0))
    $active_category_id = $_GET['category_id'];
  else
    $active_category_id = -1;

  $catalog = new catalog($dbpdo);
  ?>
  <div id="main" role="main">
    <div id="catalog-category-list">
      <div class="content">
        <div class="category<?=$active_category_id === -1 ? ' active' : '' ?>" id="category-all">
          <div class="content">
            <a href="<?=PREFIX ?>/">All categories</a>
          </div>
        </div>
        <?php
        $categories = array();
        foreach($catalog->categories as $category_id)
	  {
	    $category = new category($dbpdo, $category_id);
	    $categories[$category_id] = $category;
	    $category->display(false);
	  }
        ?>
      </div>
    </div>
    <div id="catalog-class-list">
      <div class="content">
        <?php
          if($active_category_id == -1)
	    foreach($categories as $category_id => $category)
	      $category->display(true);
	  else
	    $categories[$active_category_id]->display();
        ?>
      </div>
      <div id="separate-main-footer">
      </div>
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
