<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * delete.php - deletes a feed and all items
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

$result = fof_do_query("delete from $FOF_FEED_TABLE where id = $feed");
$result = fof_do_query("delete from $FOF_ITEM_TABLE where feed_id = $feed");

Header("Location: " . dirname($_SERVER['PHP_SELF']) . "/");
?>
