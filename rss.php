<?php
header("Content-Type:application/rss+xml; charset=utf-8");
require_once('init.php');
$xml = '<?xml version="1.0" encoding="utf-8"?>' ."\n";
echo $xml;
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<atom:link href="http://ureddit.com/rss.php" rel="self" type="application/rss+xml" />
<title>University of Reddit Classes</title>
<link>http://www.ureddit.com</link>
<description>University of Reddit's Classes RSS Feed</description>
<lastBuildDate><?=date("D, d M Y H:i:s O") ?></lastBuildDate>
<language>en-us</language>
<?php
$classes = $dbpdo->query("SELECT * FROM `objects` WHERE `type`= ? ORDER BY `id` DESC", array('class'));
foreach($classes as $objrow)
{
  try
    {
      $class = new course($dbpdo, $objrow['id']);
      if($class->get_attribute_value('status') == '0')
        continue;
      $desc = $class->get_attribute_value('description');
      echo "<item>\n";
      echo "\t<title>" . str_replace('&','&amp;',stripslashes($class->value)) . "</title>\n";
      echo "\t<link>http://ureddit.com/class/" . $class->id . "</link>\n";
      echo "\t<guid>http://ureddit.com/class/" . $class->id . "</guid>\n";
      echo "\t<pubDate>" . date("D, d M Y H:i:s O",strtotime($class->created)) . "</pubDate>\n";
      echo "\t<description>" . str_replace('&','&amp;',stripslashes($desc)) . "</description>\n";
      echo "</item>\n";
    }
  catch (ObjectAttributeNotFoundException $e)
    {
    }
}
?>
</channel>
</rss>