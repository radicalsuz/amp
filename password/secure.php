<?php
/* if ($subdir2) {
require ("$ConfigPath3");}
else */
//if (!$MM_secure) {
//if ($standalone != 1)
//{require ("$ConfigPath2");}}
$cfgServerHost = $MM_HOSTNAME;             // MySQL hostname
$cfgServerUser = $MM_USERNAME;                  // MySQL user
$cfgServerPassword = $MM_PASSWORD;                  // MySQL password
$cfgDbDatabase = $MM_DATABASE;       // MySQL database name containing phpSecurePages table


/**************************************************************/
/*              phpSecurePages version 0.27 beta              */
/*           Written by Paul Kruyt - phpSP@xs4all.nl          */
/*             http://www.phpSecurePages.f2s.com/             */
/**************************************************************/
/*           Start of phpSecurePages Configuration            */
/**************************************************************/


/****** Installation ******/

$cfgProgDir =  '/password/';
  // location of phpSecurePages calculated from the root of the server
  // Example: if you installed phpSecurePages on http://www.mydomain.com/phpSecurePages/
  // the value would be $cfgProgDir = '/phpSecurePages/'
$cfgIndexpage = 'index.php';
  // page to go to, if login is cancelled
  // Example: if your main page is http://www.mydomain.com/index.php
  // the value would be $cfgIndexpage = '/index.php'

  // E-mail adres of the site administrator
  // (This is being showed to the users on an error, so you can be notified by the users)
$noDetailedMessages = true;
  // Show detailed error messages (false) or give one single message for all errors (true).
  // If set to 'false', the error messages shown to the user describe what went wrong.
  // This is more user-friendly, but less secure, because it could allow someone to probe
  // the system for existing users.
//$passwordEncryptedWithMD5 = false;		// Set this to true if the passwords are encrypted with the
                                          // MD5 algorithm
                                          // (not yet implanted, expect this in a next release)
$languageFile = 'lng_english.php';        // Choose the language file
$bgImage = 'woods.jpg';                 // Choose the background image
$bgRotate = true;                         // Rotate the background image from list
                                          // (This overrides the $bgImage setting)


/****** Lists ******/
// List of backgrounds to rotate through
$backgrounds[] = 'woods.jpg';
$backgrounds[] = 'bear.jpg';
$backgrounds[] = 'coast.jpg';


/****** Database ******/
$useDatabase = true;                     // choose between using a database or data as input

/* this data is necessary if a database is used */

$cfgServerPort = '';                      // MySQL port - leave blank for default port



$cfgDbTableUsers = 'users';         // MySQL table name containing phpSecurePages user fields
$cfgDbLoginfield = 'name';                // MySQL field name containing login word
$cfgDbPasswordfield = 'password';         // MySQL field name containing password
$cfgDbUserLevelfield = 'permission';       // MySQL field name containing user level
  // Choose a number which represents the category of this users authorization level.
  // Leave empty if authorization levels are not used.
  // See readme.txt for more info.
$cfgDbUserIDfield = 'id';        // MySQL field name containing user identification
  // enter a distinct ID if you want to be able to identify the current user
  // Leave empty if no ID is necessary.
  // See readme.txt for more info.


/****** Database - PHP3 ******/
/* information below is only necessary for servers with PHP3 */
$cfgDbTableSessions = 'phpSP_sessions';
  // MySQL table name containing phpSecurePages sessions fields
$cfgDbTableSessionVars = 'phpSP_sessionVars';
  // MySQL table name containing phpSecurePages session variables fields


/****** Data ******/
$useData = false;                          // choose between using a database or data as input

/* this data is necessary if no database is used */
$cfgLogin[1] = '';                        // login word
$cfgPassword[1] = '';                     // password
$cfgUserLevel[1] = '';                    // user level
  // Choose a number which represents the category of this users authorization level.
  // Leave empty if authorization levels are not used.
  // See readme.txt for more info.
$cfgUserID[1] = '';                       // user identification
  // enter a distinct ID if you want to be able to identify the current user
  // Leave empty if no ID is necessary.
  // See readme.txt for more info.

$cfgLogin[2] = '';
$cfgPassword[2] = '';
$cfgUserLevel[2] = '';
$cfgUserID[2] = '';

$cfgLogin[3] = '';
$cfgPassword[3] = '';
$cfgUserLevel[3] = '';
$cfgUserID[3] = '';


/**************************************************************/
/*             End of phpSecurePages Configuration            */
/**************************************************************/


// https support
if (getenv("HTTPS") == 'on') {
	$cfgUrl = 'https://';
} else {
	$cfgUrl = 'http://';
}

// getting other login variables
$cfgHtmlDir = $cfgUrl . getenv("HTTP_HOST") . $cfgProgDir;
//$cfgProgDir = getenv("DOCUMENT_ROOT") . $cfgProgDir;
$cfgProgDir = '../password/';
//if ($MM_secure) {$cfgProgDir = 'password/';}
//echo getenv("DOCUMENT_ROOT");
if (isset($message)) $messageOld = $message;
$message = false;

// Create a constant that can be checked inside the files to be included.
// This gives an indication if secure.php has been loaded correctly.
define("LOADED_PROPERLY", true);

// include functions and variables
function admEmail() {
	// create administrators email link
	global $admEmail;
	return("<A HREF='mailto:$admEmail'>$admEmail</A>");
}

include($cfgProgDir . "lng/" . $languageFile);
include($cfgProgDir . "session.php");


// choose between login or logout
if (isset($logout) && (is_null($_GET["logout"]) || is_null($_POST["logout"]))) {
	// logout
	include($cfgProgDir . "logout.php");
} else {
	// loading login check
	include($cfgProgDir . "checklogin.php");
}
?>