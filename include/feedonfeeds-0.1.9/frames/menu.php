<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * menu.php - upper right menu for frames mode
 *
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>feed on feeds - control panel</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-frames.css" media="screen" />
		<script src="../fof.js" type="text/javascript"></script>

		<script language="javascript" type="text/javascript">
// this one doesn't quite work yet, supposed to be an onResize
// handler for body, works in Mozilla, but IE and Safari give me
// problems.  it remembers the frame sizes transparently in a cookie.
function saveLayout()
{
	expires = new Date()
	exptime = expires.getTime()
	exptime += (10 * 365 * 24 * 60 * 60 * 1000)
	expires.setTime(exptime)

	c = top.document.getElementById('hframeset').cols + '$' + top.document.getElementById('vframeset').rows;

	document.cookie = "fof_layout=" + c + "; expires=" + expires.toGMTString();
}
		</script>

		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
		<base target="items" />
	</head>

	<body id="menu-page">

<div class="menu">
<ul>
<li><a href="view.php">view new items</a></li><li><a href="view.php?what=all&amp;how=paged">view all items, paged</a></li><li><a href="view.php?what=all&amp;when=today">view today's items</a></li>
</ul>

<ul>
<li><a href="javascript:parent.items.flag_all()">flag all items</a></li><li><a href="javascript:parent.items.unflag_all()">unflag all items</a></li>
</ul>

<ul>
<li>flagged items:</li><li><a href="javascript:parent.items.mark_read()">mark as read</a></li><li><a href="javascript:parent.items.mark_unread()">mark as unread</a></li>
</ul>

<ul>
<li><a href=".." target="_top"><b>panel</b></a></li><li><a href="add.php"><b>add feeds</b></a></li><li><a href="javascript:parent.items.flag_all();parent.items.mark_read()"><b>mark all read</b></a></li><li><a href="javascript:parent.items.location.reload()"><b>refresh view</b></a></li>
<li><a href="http://minutillo.com/steve/feedonfeeds/"><b>about</b></a></li>
</ul>
</div>

</body></html>
