<?php

require_once('../init.php');

if(!logged_in())
    send_user_to("/login.php");

$class = new course($dbpdo, $_GET['id']);
$user = new user($dbpdo, $_SESSION['user_id']);

$class->get_teachers();
$class->get_owner();

if($class->owner != $user->id && !in_array($user->id,$class->teachers))
  send_user_to("/teachers/index.php");

define('ga_email', config::google_analytics_email);
define('ga_password',config::google_analytics_password);
define('ga_profile_id',config::google_analytics_profile_id);

require 'gapi.class.php';

$ga = new gapi(ga_email,ga_password);

$today = strtotime("now");
$date_ranges = array(
		     array(
			   'title' => "Traffic: Last 7 Days",
			   'start' => date("Y-m-d",$today-(60*60*24*7)), 
			   'end' => date("Y-m-d",$today)
			   ),
		     array(
			   'title' => "Traffic: Last 14 Days",
			   'start' => date("Y-m-d",$today-(60*60*24*14)), 
			   'end' => date("Y-m-d",$today)
			   ),
		     array(
			   'title' => "Traffic: Last 30 Days",
			   'start' => date("Y-m-d",$today-(60*60*24*30)), 
			   'end' => date("Y-m-d",$today)
			   ),
		     array(
			   'title' => "Traffic: Last 60 Days",
			   'start' => date("Y-m-d",$today-(60*60*24*60)), 
			   'end' => date("Y-m-d",$today)
			   ),
		    );
$date_dimensions = array('pagepath','date');
$date_metrics = array('pageviews','uniquePageviews','visits');
$date_sort = 'date';
$filter = 'pagePath =~ ^/c(lass/)?[0-9]+ && pagePath =~ ' . $class->id;

$results = array();

foreach($date_ranges as $date_range)
  {
    $stats = array();
    $ga->requestReportData(ga_profile_id,$date_dimensions,$date_metrics,$date_sort,$filter,$date_range['start'],$date_range['end'],1,1000);
    foreach($ga->getResults() as $result)
      {
	$date = $result->getDate();
	if(!isset($stats[$date]))
	  $stats[$date] = array();
	foreach($date_metrics as $metric)
	  {
	    if(!isset($stats[$date][$metric]))
	      $stats[$date][$metric] = 0;
	    $get_func = 'get' . ucfirst($metric);
	    $stats[$date][$metric] += $result->$get_func();
	  }
      }
    $results[] = $stats;
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
  <title>University of Reddit</title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <?php include('../includes.php'); ?>
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
   google.load('visualization', '1.0', {'packages':['corechart']});
   google.setOnLoadCallback(drawCharts);
function drawCharts()
{
  <?php
  $count = 0;
  foreach($results as $stats)
    {
      ?>
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Date');

      <?php
      foreach($date_metrics as $metric)
	{
	  echo "data.addColumn('number', '$metric');\n";
	}
      ?>
      data.addRows([
		    <?php
		    foreach($stats as $date => $metrics)
		      {
			$date = substr($date, 4);
			$date = substr($date,0,2) . '-' . substr($date,2);
			echo '[\'' . $date . '\',' . implode(',',$metrics) . '],' . "\n";
		      }
		    ?>
		    ]);

      // Set chart options
      var options = {'title':'<?=$date_ranges[$count]["title"] ?>',
		     'width':800,
		     'height':300};
      
      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.LineChart(document.getElementById('chart<?=$count ?>'));
      chart.draw(data, options);
      <?php
      $count++;
    }
  ?>
}
</script>
   </head>

<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('../header.php');
  require_once('../social.php');

  ?>
  <div id="main" role="main">
    <div id="teach">
      <div class="content">
        <h1>Statistics: <?=$class->value ?></h1>

<?php
for($i = 0; $i < $count; $i++)
  {
    ?>
    <div id="chart<?=$i ?>"></div>
    <?php
  }
?>

      </div>
    </div>
    <div id="teach-side">
      <div class="content" style="border-bottom: 3px solid #232323">
    <?php include('tools.php'); ?>
      </div>
    </div>
    <div id="separate-main-footer">
    </div>
  </div>
  <?php require_once('../footer.php'); ?>
</body>
</html>


<?php

    die();

?>

<table>
<tr>
  <th>Pagepath</th>
  <th>Date</th>
  <th>Pageviews</th>
  <th>Visits</th>
</tr>
<?php
$myResults = array();
foreach($ga->getResults() as $result)
{
?>
<tr>
  <td><?php echo $result ?></td>
    <td><?php echo $result->getDate() ?></td>
    <td><?php echo $result->getVisits() ?></td>
    <td><?php echo $result->getPageviews() ?></td>
    <td><?php echo $result->getUniquePageviews() ?></td>
</tr>
<?php
  $class = "".$result;
  $matches = array();
  preg_match('/^\/c(lass\/)?([0-9]+)/i',$class,$matches);
  $id = $matches[2];
  if(!isset($myResults[$id]))
    {
      $myResults[$id] = array((int)$result->getVisits(), (int)$result->getPageviews(), (int)$result->getUniquePageviews());
    }
  else
    {
      $myResults[$id][0] += (int)$result->getVisits();
      $myResults[$id][1] += (int)$result->getPageviews();
      $myResults[$id][2] += (int)$result->getUniquePageviews();
    }
}

foreach($myResults as $class => &$stats):
?>
<tr>
  <td><?php echo $class ?></td>
  <td><?php echo $stats[0] ?></td>
  <td><?php echo $stats[1] ?></td>
  <td><?php echo $stats[2] ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Pageviews</th>
  <td><?php echo $ga->getPageviews() ?>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getVisits() ?></td>
</tr>
<tr>
  <th>Results Updated</th>
  <td><?php echo $ga->getUpdated() ?></td>
</tr>
</table>