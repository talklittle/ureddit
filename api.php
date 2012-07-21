<?php

require_once('init.php');

if(empty($_GET))
  {
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
  <?php include('includes.php'); ?>
  <style type="text/css">
  p {
    margin: 0 0 5px 50px;
  }

  h1 {
    margin-top: 10px;
    margin-bottom: 10px;
  }

  h2 {
    margin-left: 25px;
    margin-bottom: 3px;
  }

  h3 {
    margin-left: 30px;
    margin-bottom: 3px;
  }

  ul {
    margin: 10px 0 10px 100px;
  }
-->
</style>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('header.php');
  require_once('social.php');

  ?>
  <div id="main" role="main">
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
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>

<?php
  }
else
  {
    $type = isset($_GET['type']) ? $_GET['type'] : NULL;
    $id = isset($_GET['id']) ? $_GET['id'] : NULL;
    $api = new api($dbpdo, $type, $id);
  }

?>

