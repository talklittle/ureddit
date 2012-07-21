<?php

require_once('init.php');

if(empty($_GET))
  {
    $params['title'] = 'University of Reddit : API';
    require('header2.php');
?>
    <div id="help">
      <div class="content">
<?php

$str = <<<EOD
#UReddit API

## Use

**Requests**

  Send a request to `http://ureddit.com/api` with any parameters as HTTP GET parameters. You must always supply a `type` paramater and, for all types listed below except `catalog`, you must provide an `id` parameter.

**Parameters**

The following values can be provided for the `type` parameter

  * `catalog`: returns a list of category `id`s.
  * `category`: requires `id`. Returns the category name and a list of the `id`s of all classes in that category.
  * `class`: requires `id`. Returns all class information, including the roster (a list of user `id`s), lectures (a list of lecture `id`s), and teachers (a list of user `id`s).
  * `lecture`: requires `id`. Returns the lecture name and description and any associated links (a list of link `id`s).
  * `link`: requires `id`. Returns the link title and URL.
  * `user`: requires `id`. Returns the user username, registration date, and schedule.

**JSONP**

Add a `jsonp` GET parameter whose value is the name of the callback function in which the JSON is to be wrapped (e.g. `jsonp=parseFunction`) and the response will be formatted as per the JSONP standard.

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
    $jsonp = isset($_GET['jsonp']) ? $_GET['jsonp'] : '';
    $api = new api($dbpdo, $jsonp, $type, $id);
  }

?>

