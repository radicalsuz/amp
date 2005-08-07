<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * feeds.php - feed list for frames mode
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

	<head>
		<title><?php echo $title ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-frames.css" media="screen" />
		<script src="../fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
		<base target="items" />
	</head>

<body id="panel-page">

<div id="feeds">

<?php

$order = $_GET['order'];
$direction = $_GET['direction'];

if(!isset($order))
{
	$order = "title";
}

if(!isset($direction))
{
	$direction = "asc";
}

$feeds = fof_get_feeds($order, $direction);

foreach($feeds as $row)
{
    $n++;
    $unread += $row['unread'];
}

?>

<p><?php echo $n ?> feeds, <?php echo $unread?> new items.</p>

<table cellspacing="0" cellpadding="1" border="0">

<tr class="heading">

<?php

$title["age"] = "sort by last update time";
$title["unread"] = "sort by number of unread items";
$title["title"] = "sort by feed title";

foreach (array("age", "unread", "title") as $col)
{
	echo "<td><nobr><a title=\"$title[$col]\"target=\"_self\" href=\"feeds.php?order=$col";

	if($col == $order && $direction == "asc")
	{
		echo "&amp;direction=desc\">";
	}
	else
	{
		echo "&amp;direction=asc\">";
	}


	if($col == "unread")
	{
		echo "<span class=\"unread\">#</span>";
	}
	else
	{
		echo $col;
	}

	if($col == $order)
	{
		echo ($direction == "asc") ? "&darr;" : "&uarr;";
	}

	echo "</nobr></a></td>";
}

?>

<td></td>
</tr>

<?php

foreach($feeds as $row)
{

	$id = $row['id'];
	$url = htmlspecialchars($row['url']);
	$title = htmlspecialchars($row['title']);
	$link = htmlspecialchars($row['link']);
	$description = $row['description'];
	$age = fof_rss_age($row['url']);
	$unread = $row['unread'];
	$items = $row['items'];
	$agestr = $row['agestr'];
	$agestrabbr = $row['agestrabbr'];

	if(++$t % 2)
	{
		print "<tr class=\"odd-row\">";
	}
	else
	{
		print "<tr>";
	}

	$u = "view.php?feed=$id";
	$u2 = "view.php?feed=$id&amp;what=all&amp;how=paged";

	print "<td><span title=\"$agestr\">$agestrabbr</span></td>";

	print "<td class=\"nowrap\">";

	if($unread)
	{
		print "<a class=\"unread\" title=\"new items\" href=\"$u\">$unread</a>/";
	}

	print "<a href=\"$u2\" title=\"all items\">$items</a>";

	print "</td>";


	print "<td><a href=\"$link\" title=\"home page\"><b>$title</b></a> <a href=\"$url\" title=\"feed URL\">(f)</a></td>";

	print "<td><nobr><a href=\"update.php?feed=$id\" title=\"update\">u</a>";
	print " <a href=\"delete.php?feed=$id\" title=\"delete\" onclick=\"return confirm('What-- Are you SURE?')\">d</a></nobr></td>";

	print "</tr>";
}


?>

</table>

</div>
</body>

</html>
