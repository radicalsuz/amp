<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * config.php - modify this file with your database settings
 *
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */


// Difference, in hours, between your server and your local time zone.

define('FOF_TIME_OFFSET', 0);


// Database connection information.  Host, username, password, database name.

define('FOF_DB_HOST', "AMP_DB_HOST");
define('FOF_DB_USER', "AMP_DB_USER");
define('FOF_DB_PASS', "AMP_DB_PASS");
define('FOF_DB_DBNAME', "AMP_DB_NAME");


// The rest you should not need to change


// How many posts to show by default in paged mode

define('FOF_HOWMANY', 50);


// How long to keep posts
// if this is defined, FoF will delete posts after:
// A) they are read
// 2) they were cached more than this number of days ago
// if this is not defined, it will keep them forever.

define('FOF_KEEP_DAYS', 30);


// DB table names

define('FOF_FEED_TABLE', "px_feeds");
define('FOF_ITEM_TABLE', "px_items");


// Turn GZip on in Magpie

define('MAGPIE_USE_GZIP', true);


// Find ourselves and the cache dir

if (!defined('DIR_SEP')) {
	define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if (!defined('FOF_DIR')) {
    define('FOF_DIR', dirname(__FILE__) . DIR_SEP);
}

if (!defined('FOF_CACHE_DIR'))
{
    define('FOF_CACHE_DIR', FOF_DIR . DIR_SEP . "cache");
}

?>
