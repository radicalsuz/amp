<?php
#generic update page
$modid = "45";
$mod_name = "rss";

require("Connections/freedomrising.php");

include_once("FeedOnFeeds/init.php");


include ("header.php");

print "<h2>RSS Aggregator</h2>";

$feed = $_GET['feed'];

$sql = "select url, id, title from " . FOF_FEED_TABLE;

if($feed)
{
  $sql .= " WHERE (isNull(service) OR service='Content') AND id = $feed";
}

$sql .= " order by title";

$result = fof_do_query($sql);

while($row = mysql_fetch_array($result))
{
	$title = $row['title'];
	$id = $row['id'];
	print "Updating <b>$title</b>...";

	$count = fof_update_feed($row['url']);

	print "done. ";

	if($count)
	{
		print "<b><font color=red>$count new items</font></b>";
	}
	print "<br>";
}
?>
<BR>
<?php
include ("footer.php");
?>
