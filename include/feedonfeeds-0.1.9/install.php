<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * install.php - creates tables and cache directory, if they don't exist
 *
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

$installing = true;

include_once("init.php");
header("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head><title>feed on feeds - installation</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof.css" media="screen" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
	</head>

	<body id="panel-page">


Creating tables...<br>
<?php

$query = <<<EOQ
CREATE TABLE `$FOF_FEED_TABLE` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(250) NOT NULL default '',
  `title` varchar(250) NOT NULL default '',
  `link` varchar(250) default NULL,
  `description` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
EOQ;

if(!fof_do_query($query, 1) && mysql_errno() != 1050)
{
	exit ("Can't create table.  MySQL says: <b>" . mysql_error() . "</b><br>" );
}

$query = <<<EOQ
CREATE TABLE `$FOF_ITEM_TABLE` (
  `id` int(11) NOT NULL auto_increment,
  `feed_id` int(11) NOT NULL default '0',
  `timestamp` timestamp(14) NOT NULL,
  `link` text,
  `title` varchar(250) default NULL,
  `content` text,
  `dcdate` text,
  `dccreator` text,
  `dcsubject` text,
  `read` tinyint(4) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
EOQ;

if(!fof_do_query($query, 1) && mysql_errno() != 1050)
{
	exit ("Can't create table.  MySQL says: <b>" . mysql_error() . "</b><br>" );
}

?>
Tables exist.<br><br>

Creating indexes...<br>

<?php
if(!fof_do_query("ALTER TABLE `$FOF_ITEM_TABLE` ADD INDEX `feed_id_idx` ( `feed_id` )", 1) && mysql_errno() != 1061)
{
	exit ("Can't create index.  MySQL says: <b>" . mysql_error() . "</b><br>" );
}

if(!fof_do_query("ALTER TABLE `$FOF_ITEM_TABLE` ADD INDEX `read_idx` ( `read` )", 1) && mysql_errno() != 1061)
{
	exit ("Can't create index.  MySQL says: <b>" . mysql_error() . "</b><br>" );
}
?>
Indexes exist.<br><br>

Checking cache directory...<br>
<?php

if ( ! file_exists( "cache" ) )
{
	$status = @mkdir( "cache", 0755 );

	if ( ! $status )
	{
		echo "Can't create directory <code>" . getcwd() . "/cache/</code>.<br>You will need to create it yourself, and make it writeable by your PHP process.<br>Then, reload this page.";
		exit;
	}
}

if(!is_writable( "cache" ))
{
		echo "The directory <code>" . getcwd() . "/cache/</code> exists, but is not writable.<br>You will need to make it writeable by your PHP process.<br>Then, reload this page.";
		exit;
}

?>

Cache directory exists and is writable.<br><br>

Encodings will be translated by:

<?php
if ( substr(phpversion(),0,1) == 5) {
	echo "<b>PHP5 XML parser</b>.  We're going to try to use the XML parser itself to handle encodings.<br><br>";
}
else {
	if(function_exists('iconv'))
	{
		echo '<b>iconv</b>.  You have PHP4, and the <a href="http://us4.php.net/manual/en/ref.iconv.php">iconv module</a> installed.<br><br>';
	}
	else if(function_exists('mb_convert_encoding'))
	{
		echo '<b>mbstring</b>.  You have PHP4, and the <a href="http://us4.php.net/manual/en/ref.mbstring.php">mbstring module</a> installed.<br><br>';
	}
	else
	{
		echo '<b>PHP4 XML parser</b>.  You have PHP4, but neither iconv nor mbstring is intalled.  Only UTF-8, ISO-8859-1, and US-ASCII feeds are going to work.  Ask your host to install iconv for best results.<br><br>';
	}
}
?>

Setup complete! <a href=".">Go to the control panel and start subscribing.</a>

</body></html>
