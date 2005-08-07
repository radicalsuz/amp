<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * update-quiet.php - updates all feeds without producing output
 *
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

ob_start();
include_once("init.php");

$result = fof_do_query("select url, id, title from " . FOF_FEED_TABLE . " order by title");

while($row = mysql_fetch_array($result))
{
	$title = $row['title'];
	$id = $row['id'];
	fof_update_feed($row['url']);
}

ob_end_clean();
?>
