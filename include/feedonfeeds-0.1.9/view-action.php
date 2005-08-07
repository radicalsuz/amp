<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * view-action.php - marks selected items as read (or unread)
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

if($_POST['action'] == 'read')
{
	$sql = "update " . FOF_ITEM_TABLE . " set `read` = 1, timestamp=timestamp where ";
}

if($_POST['action'] == 'unread')
{
	$sql = "update " . FOF_ITEM_TABLE . " set `read` = null, timestamp=timestamp where ";
}

$first = true;

while (list ($key, $val) = each ($_POST))
{
	if($val == "checked")
	{
		$key = substr($key, 1);
		if(!$first)
		{
			$sql .= " or ";
		}

		$first = false;

		$sql .= " `id` = $key ";
	}

}

if(!$first) fof_do_query($sql);

header("Location: " . urldecode($_POST['return']));

?>
