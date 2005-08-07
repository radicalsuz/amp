<?php

/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * view.php - views items based on query parameters
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

if($_GET['how'] == 'paged' && !isset($_GET['which']))
{
	$which = 0;
}
else
{
	$which = $_GET['which'];
}

$title = fof_view_title($_GET['feed'], $_GET['what'], $_GET['when'], $which, $_GET['howmany']);
$noedit = $_GET['noedit'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title><?php echo $title ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof.css" media="screen" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
	</head>

	<body id="view-page">

<?php

if(!$noedit)
{
?>
		<form name="items" action="view-action.php" method="post">
		<input type="hidden" name="action" />
		<input type="hidden" name="return" />

		<?php readfile("view-menu.html"); ?>

<?php

	$links = fof_get_nav_links($_GET['feed'], $_GET['what'], $_GET['when'], $which, $_GET['howmany']);

	if($links)
	{
?>
		<div class="nav"><?php echo $links ?></div>


<?php
	}
}


$result = fof_get_items($_GET['feed'], $_GET['what'], $_GET['when'], $which, $_GET['howmany']);

foreach($result as $row)
{
	$items = true;

	$feed_link = htmlspecialchars($row['feed_link']);
	$feed_title = htmlspecialchars($row['feed_title']);
	$feed_description = htmlspecialchars($row['feed_description']);
	$item_id = $row['item_id'];
	$item_link = htmlspecialchars($row['item_link']);
	$item_title = htmlspecialchars($row['item_title']);
	$item_content = fof_balanceTags($row['item_content']);
	$item_read = $row['item_read'];
	$timestamp =  date("F j, Y, g:i a", $row['timestamp'] - (FOF_TIME_OFFSET * 60 * 60));
	$dccreator = $row['dccreator'];
	$dcdate = $row['dcdate'];
	$dcsubject = $row['dcsubject'];


	print '<div class="item">';
	print '<div class="header">';

	print "<a class=\"headline\" href=\"$item_link\">$item_title</a> ";
	print "<a class=\"feed\" href=\"$feed_link\" title=\"$feed_description\">$feed_title</a>";

	print '<span class="meta">';

	if($dccreator)
	{
		print "by $dccreator ";
	}

	if($dcsubject)
	{
		print "on $dcsubject ";
	}

	if($dcdate)
	{
				$dcdate = date("F j, Y, g:i a", parse_w3cdtf($dcdate) + $asec - (FOF_TIME_OFFSET * 60 * 60));

		print "at $dcdate ";
	}
	print "(cached at $timestamp)</span>";


	if(!$noedit)
	{
		echo ' <span class="controls">';
		print "<a href=\"javascript:flag_upto('c$item_id')\">flag all up to this item</a> ";
		print "<input type=\"checkbox\" name=\"c$item_id\" value=\"checked\" />";
		echo '</span>';
	}
	print "<div class=\"clearer\"></div><br/></div><div class=\"body\">$item_content</div></div>";
}

if(!$items)
{
echo "No items found.";
}

if(!$noedit)
{
?>
		</form>
<?php
	if($links)
	{
?>
		<div class="nav"><?php echo $links ?></div>

<?php
	}

	readfile("view-menu.html");
}
?>

	</body>
</html>
