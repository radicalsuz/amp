<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * index.php - frameset for frames mode
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>feed on feeds</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>

<?php
/*
if(isset($_COOKIE['fof_layout']))
{
	$cookie_info = explode("$", $_COOKIE['fof_layout']);
	$cols = $cookie_info[0];
	$rows = $cookie_info[1];
}
else
*/
{
	$cols = "40%, *";
	$rows = "11%, *";
}
?>

<frameset id="hframeset" cols="<?php echo $cols?>" >
<frameset id="vframeset" rows="<?php echo $rows?>" >
<frame src="menu.php" name="controls" />
<frame src="feeds.php"  name="menu" />
</frameset>
<frame src="view.php"  name="items" />
</frameset>
</html>
