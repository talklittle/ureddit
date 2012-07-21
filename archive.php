<?php
require_once('init.php');

$params['title'] = 'University of Reddit : Archive';
require('header2.php');

if(isset($_GET['category_id']) && count($dbpdo->query("SELECT `id` FROM `objects` WHERE `id` = ? AND `type` = 'category' LIMIT 1", array($_GET['category_id'])) != 0))
  {
    $active_category_id = $_GET['category_id'];
  }
else
  {
    $active_category_id = -1;
  }

$catalog = new catalog($dbpdo);

?>
    <div id="catalog-category-list">
      <div class="content">
        <div class="category<?=$active_category_id === -1 ? ' active' : '' ?>" id="category-all">
          <div class="content">
            <a href="<?=PREFIX ?>/<?=strpos($_SERVER['PHP_SELF'], 'archive') !== false ? 'archive' : '' ?>/">All categories</a>
          </div>
        </div>
        <?php
        $categories = array();
        foreach($catalog->categories as $category_id)
	  {
	    $category = new category($dbpdo, $category_id);
	    $categories[$category_id] = $category;
	    $category->display(false,'completed');
	  }
        ?>
      </div>
    </div>
    <div id="catalog-class-list">
      <div class="content">
        <?php
          include('socialbuttons.php');
          if($active_category_id == -1)
	    foreach($categories as $category_id => $category)
	      $category->display(true, 'completed');
	  else
	    $categories[$active_category_id]->display(true, 'completed', true);
        ?>
      </div>
    </div>
<?php require_once('footer2.php'); ?>