<?php

$ampasp = (defined('AMP_HOSTED') && AMP_HOSTED) ? '1' : '0';

if (defined( 'AMP_BASE_PATH' ) ) $base_path = AMP_BASE_PATH;

// Not Quite Sure what these do.
$MX_type = "type";
$MX_top = 1;

// Superceded. Custom path is incorporated into include_path. Use
// require_once('SiteConfig.inc.php') first, failing over to
// require_once('config.php').
$ConfigPath = AMP_LOCAL_PATH . "custom/config.php";
$ConfigPath2 = $ConfigPath;
$ConfigPath3 = $ConfigPath;

$PHP_SELF = $_SERVER['PHP_SELF'];

$MM_sysvar_mq = (get_magic_quotes_gpc()) ? true : false;

#load menu class	
if (file_exists($base_path."Connections/menu.class.php")) {
    require_once($base_path."Connections/menu.class.php");
    $obj = new Menu;
} 

# Get system vars
$getsysvars = $dbcon->CacheExecute("SELECT * FROM sysvar WHERE id = 1")
    or die("Couldn't fetch system settings: " . $dbcon->ErrorMsg()); 

$SystemSettings = $getsysvars->FetchRow();

$SiteName            = $SystemSettings["websitename"];
$Web_url             = $SystemSettings["basepath"];
//Set caching to 0 for AMP-authenticated system side users
$cacheSecs           = (isset($_SERVER['REMOTE_USER'])&&($_SERVER['REMOTE_USER']))?0:$SystemSettings["cacheSecs"];
$admEmail            = $SystemSettings["emfaq"];			//needed for admin only
$MM_email_usersubmit = $SystemSettings["emendorse"];			//User Submitted Article
$MM_email_from       = $SystemSettings["emfrom"];			//return email web sent emails
$meta_description    = $SystemSettings["metadescription"];		//meta desc
$meta_content        = $SystemSettings['metacontent'];			//meta content
$systemplate_id      = $SystemSettings['template'];
		
#SET DATABASE CACHING
$dbcon->cacheSecs = $cacheSecs;

$MM_USERNAME = AMP_DB_USER;
$MM_HOSTNAME = AMP_DB_HOST;
$MM_PASSWORD = AMP_DB_PASS;
$MM_DATABASE = AMP_DB_NAME;

if (!isset($sysversion)) {
    $sysversion = '3.4-dev';
}

?>
