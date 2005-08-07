<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * ompl.php - exports subscription list as OPML
 *
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

header("Content-Type: text/xml; charset=utf-8");
include_once("init.php");

echo '<?xml version="1.0"?>';
?>

<opml>
  <body>
<?php
$result = fof_do_query("select url, title, link, description from " . FOF_FEED_TABLE . " order by title");

while($row = mysql_fetch_array($result))
{
	$url = htmlspecialchars($row['url']);
	$title = htmlspecialchars($row['title']);
	$link = htmlspecialchars($row['link']);
	$description = htmlspecialchars($row['description']);

	echo <<<HEYO
    <outline description="$description"
             htmlurl="$link"
             title="$title"
             xmlUrl="$url"
    />

HEYO;
}
?>
  </body>
</opml>
