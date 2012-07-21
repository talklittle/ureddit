<?php

require_once('init.php');

if(empty($_GET))
  {
    $params['title'] = 'University of Reddit : Help';
    require('header2.php');
?>
    <div id="help">
      <div class="content">
<?php

$str = <<<EOD
#UReddit API

## Use

**Requests**

  Send a request to `http://ureddit.com/api` with any parameters as HTTP GET variables. You must always supply a `type` variable and, for all types listed below except `catalog`, you must provide an `id` variable.

**Parameters**

  * `catalog`: returns a list of category `id`s.
  * `category`: requires `id`. Returns the category name and a list of the `id`s of all classes in that category.
  * `class`: requires `id`. Returns all class information, including the roster (a list of user `id`s), lectures (a list of lecture `id`s), and teachers (a list of user `id`s).
  * `lecture`: requires `id`. Returns the lecture name and description and any associated links (a list of link `id`s).
  * `link`: requires `id`. Returns the link title and URL.
  * `user`: requires `id`. Returns the user username, registration date, and schedule.

**Output**

All data is returned encoded in JSON.

EOD;

echo process($str);
?>
      </div>
    </div>
<?php
    require_once('footer2.php');
  }
else
  {
    $type = isset($_GET['type']) ? $_GET['type'] : NULL;
    $id = isset($_GET['id']) ? $_GET['id'] : NULL;
    $api = new api($dbpdo, $type, $id);
  }

?>

