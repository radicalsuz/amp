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

include_once("../init.php");
header("Content-Type: text/html; charset=utf-8");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head><title>feed on feeds - delete</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-frames.css" media="screen" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>
<body>

<?

$feed = $_GET['feed'];

$result = fof_do_query("delete from $FOF_FEED_TABLE where id = $feed");
$result = fof_do_query("delete from $FOF_ITEM_TABLE where feed_id = $feed");

?>

Deleted.  <a href="view.php">Return to new items.</a>

</body></html>
