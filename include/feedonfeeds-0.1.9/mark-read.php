<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * mark-read.php - marks a single item or all items in a feed as read
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

$feed = $_GET['feed'];
$item = $_GET['item'];

$sql = "update " . FOF_ITEM_TABLE . " set `read` = 1, timestamp=timestamp ";

if($feed)
{
	$sql .= 'where `feed_id` = ' . $feed;
}
else if($item)
{
	$sql .= 'where `id` = ' . $item;
}

$result = fof_do_query($sql);

if($item)
{
	header("Status: 204 Yatta");
}
else
{
	Header("Location: " . dirname($_SERVER['PHP_SELF']) . "/");
}

?>
