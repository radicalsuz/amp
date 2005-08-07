<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * update.php - updates all feeds with feedback
 *
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

include_once("../init.php");
header("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head><title>feed on feeds - update</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-frames.css" media="screen" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>
<body>

<BR>
<?php

$feed = $_GET['feed'];

$sql = "select url, id, title from " . FOF_FEED_TABLE;

if($feed)
{
  $sql .= " where id = $feed";
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

Update complete.  <a href="view.php">Return to new items.</a>

</body></html>
