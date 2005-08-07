<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * index.php - the 'control panel'
 *
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

include_once("init.php");
header("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>feed on feeds - control panel</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof.css" media="screen" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
	</head>

	<body id="panel-page">

<?php readfile("panel-menu.html"); ?>

<table class="feeds">

<?php

$feeds = fof_get_feeds();

foreach($feeds as $row)
{

	$id = $row['id'];
	$url = $row['url'];
	$title = $row['title'];
	$link = $row['link'];
	$description = $row['description'];
	$age = fof_rss_age($row['url']);
	$unread = $row['unread'];
	$items = $row['items'];
	$agestr = $row['agestr'];

	if(++$t % 2)
	{
		print "<tr class=\"odd-row\">";
	}
	else
	{
		print "<tr>";
	}

	print "<td>$agestr</td>";

	$u = "view.php?feed=$id";

	if($unread)
	{
		print "<td>&nbsp;&nbsp;(<a class=\"unread\" href=\"$u\">$unread new</a> / ";
	}
	else
	{
		print "<td>&nbsp;&nbsp;(0 new / ";
	}


	print "<a href=\"$u&amp;what=all\">$items</a>)</td>";


	print "<td>&nbsp;&nbsp;" . fof_render_feed_link($row) . "</td>";

	print "<td>&nbsp;&nbsp;<a href=\"update.php?feed=$id\">update</a></td>";
	print "<td>&nbsp;&nbsp;<a href=\"mark-read.php?feed=$id\">mark all read</a></td>";
	print "<td>&nbsp;&nbsp;<a href=\"delete.php?feed=$id\" onclick=\"return confirm('What-- Are you SURE?')\">delete</a></td>";

	print "</tr>";
}


?>

</table>

<?php readfile("panel-menu.html"); ?>

	</body>
</html>

