<?php
require_once('init.php');

if(isset($_GET['category_id']) && count($dbpdo->query("SELECT `id` FROM `objects` WHERE `id` = ? AND `type` = 'category' LIMIT 1", array($_GET['category_id'])) != 0))
  $active_category_id = $_GET['category_id'];
else
  $active_category_id = -1;

$catalog = new catalog($dbpdo);

$categories = array();
foreach($catalog->categories as $category_id)
  {
    $category = new category($dbpdo, $category_id);
    $categories[$category_id] = $category;
  }

$params['title'] = "University of Reddit" . ($active_category_id == -1 ? '' : ' : ' . $categories[$active_category_id]->value);
require('header2.php');

?>
    <div id="catalog-category-list">
      <div class="content">
        <div class="category<?=$active_category_id === -1 ? ' active' : '' ?>" id="category-all">
          <div class="content">
            <a href="<?=PREFIX ?>/">All categories</a>
          </div>
        </div>
        <?php
        foreach($categories as $category_id => $category)
	  {
	    $category->display(false);
	  }
        ?>
      </div>
    </div>
    <div id="catalog-class-list">
      <div class="content">
        <?php
          include('socialbuttons.php');
?>
	  <? echo "<div class=\"infobox\" style=\"margin-left: 5px;\">Would you like to request a class? Post your request at <a href=\"http://reddit.com/r/URedditRequests\" target=\"_blank\">/r/URedditRequests</a>!</div><br />"; ?>
<?php
          if($active_category_id == -1)
	    foreach($categories as $category_id => $category)
	      $category->display(true, 'open');
	  else
	    $categories[$active_category_id]->display(true, 'open', true);
        ?>
      </div>
    </div>
<?php require('footer2.php'); ?>