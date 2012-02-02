<?php

class catalog extends object
{
  public $categories = array();

  function __construct($dbpdo)
  {
    parent::__construct($dbpdo, 1);
    $this->get_categories();
  }

  function get_categories()
  {
    $this->get_children('category','init');
    $this->categories = $this->children['category'];
  }

  function display($expand_classes = false, $class_details = false)
  {
    $categories = array();
    foreach($this->categories as $category_id)
      {
	$category = new category($this->dbpdo, $category_id);
	$this->category_objects[] = $category;
	?>
        <div id="category<?=$this->id ?>">
          <div class="category">
            <div class="category-name">
              <?=$category->value; ?>
                <span class="showhide">
                  <a
                    onclick="$.get('<?=PREFIX ?>/category.php',{id: '<?=$category->id ?>', show: 'false', user_id: '-1', teacher: 'false'}, function(data) { $('#category<?=$category->id ?>').html(data)});"
                    class="link-showhide"
                  >[hide]</a>
                </span>
              </div>
	   <?php $category->display($expand_classes, $class_details); ?>
            </div>
          </div>
        <?php
      }
  }
}

?>