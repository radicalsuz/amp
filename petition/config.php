<?php
//  phpetition v0.2, An easy to use PHP/MySQL Petition Script
//  Copyright (C) 2001,  Mike Gifford, http://openconcept.ca
//
//  This script is free software; you can redistribute it and/or
//  modify it under the terms of the GNU General Public License
//  as published by the Free Software Foundation; either version 2
//  of the License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License http://www.gnu.org/copyleft/ for more details. 
//
//  If you distribute this code, please maintain this header.
//  Links are always appreciated!
//
// This file is used to configure your script
$mod_id = 42;
 ob_start();
$subdir=1;
  require_once("../adodb/adodb.inc.php");
  require_once("../Connections/freedomrising.php");
  require_once("$base_path"."Connections/modhierarchy.php");  
  require_once("$base_path"."Connections/templateassign.php");
include("$base_path"."header.php"); 
echo "<!-- START: config.php -->\n";

error_reporting( E_ALL ^ E_NOTICE );

	$dbhost = $MM_HOSTNAME;  	//  MySQL server hostname
	$dbuser = $MM_USERNAME;  			//  MySQL server username
	$dbpasswd = $MM_PASSWORD;	            	//  MySQL server password
	$db_name=$MM_DATABASE;			//  MySQL database name
	$db=mysql_connect("$dbhost","$dbuser","$dbpasswd");	//  MySQL server connect
	mysql_select_db($db_name,$db);

// Paths to Petition Script
// No trailing slash for any of the following paths or urls 
	//$base_path="home/sites/site7/web/new/petition";	// System Path
	$base_url="petition/";	// Web Path
	$petdir ="petition";
	
	//$base_path="home/sites/site7/web/new/petition";	// System Path
	//$base_url="http://www.unitedforpeace.org/new/petition";	// Web Path
	//$base_path ="/usr/var/www/freedomrising/seaflow2/";
// language detection and variables.
  include ("$base_path"."$petdir"."/lang.php");

$formFontColor = "black";
$requiredFormFontColor = "#800000";
$formBGcolor = "#ffffff";
$boxBGcolor = "#888888";
$boxFontColor = "#ffffff";
$lightBoxColor = "#FFFFCC";

//$openNewWindow = "target=\"_blank\"";
$openNewWindow = "";

// Script Name, Version & Credits (Please Don't Modify)
	$site_title="phpetitions";  
	$version="0.3";
	$show_version="N";
	$script_display="This script was developed with " . $site_title . " " . $version . ".  Available at http://openconcept.ca";

echo "<!-- END: config.php -->\n";
?>
