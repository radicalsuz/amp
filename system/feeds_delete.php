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
require("Connections/freedomrising.php");

include_once("feedonfeeds/init.php");

$feed = $_GET['feed'];

$result = fof_do_query("delete from $FOF_FEED_TABLE where id = $feed");
$result = fof_do_query("delete from $FOF_ITEM_TABLE where feed_id = $feed");

Header("Location: feeds_add.php");
?>
