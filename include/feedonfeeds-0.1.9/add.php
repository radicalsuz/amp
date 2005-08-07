<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * add.php - displays form to add a feed
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
<html>
<head><title>feed on feeds - add a feed</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof.css" media="screen" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>

<body id="panel-page">

<?php readfile("panel-menu.html"); ?>
<br>
<?php

$url = $_POST['rss_url'];
if(!$url) $url = $_GET['rss_url'];
$opml = $_POST['opml_url'];
$file = $_POST['opml_file'];
?>
<table border=1 cellpadding=3 cellspacing=0 bgcolor="#EEEEEE"><tr><td>
<a href="javascript:void(location.href='http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] ?>?rss_url='+escape(location))">FoF subscribe</a> - This bookmarklet will attempt to subscribe to whatever page you are on.<br>Drag it to your toolbar and then click on it when you are at a weblog you like.
</tr></td></table>
<BR><br>
<form method="post" action="add.php" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="100000">

RSS or weblog URL: <input type="text" name="rss_url" size="40" value="<?php echo $url ?>"><input type="Submit" value="Add a feed"><br><br>
</form>

<form method="post" action="add.php" enctype="multipart/form-data">
OPML URL: <input type="hidden" name="MAX_FILE_SIZE" value="100000">

<input type="text" name="opml_url" size="40" value="<?php echo $opml ?>"><input type="Submit" value="Add feeds from OPML file on the Internet"><br><br>
</form>

<form method="post" action="add.php" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="100000">
OPML filename: <input type="file" name="opml_file" size="40" value="<?php echo $file ?>"><input type="Submit" value="Upload an OPML file">

</form>

<?php
if($url) fof_add_feed($url);

if($opml)
{
	if(!$content_array = file($opml))
	{
		echo "Cannot open $opml<br>";
		return false;
	}

	$content = implode("", $content_array);

	$feeds = fof_opml_to_array($content);
}

if($_FILES['opml_file']['tmp_name'])
{
	if(!$content_array = file($_FILES['opml_file']['tmp_name']))
	{
		echo "Cannot open uploaded file<br>";
		return false;
	}

	$content = implode("", $content_array);

	$feeds = fof_opml_to_array($content);
}

foreach ($feeds as $feed)
{
	fof_add_feed($feed);
	echo "<hr size=1>";
	flush();
}

?>
<BR>

<?php readfile("panel-menu.html"); ?>

</body>
</html>
