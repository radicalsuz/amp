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
$cacheSecs           = (isset($_SERVER['REMOTE_USER']) && ($_SERVER['REMOTE_USER'])) ? 1 : $SystemSettings["cacheSecs"];
$admEmail            = $SystemSettings["emfaq"];			//needed for admin only
$MM_email_usersubmit = $SystemSettings["emendorse"];			//User Submitted Article
$MM_email_from       = $SystemSettings["emfrom"];			//return email web sent emails
$meta_description    = $SystemSettings["metadescription"];		//meta desc
$meta_content        = $SystemSettings['metacontent'];			//meta content
$systemplate_id      = $SystemSettings['template'];

define('AMP_SITE_ADMIN', $admEmail);

$encoding = isset($SystemSettings['encoding']) ? $SystemSettings['encoding'] : 'iso-8859-1';
$reg_manager = & AMP_Registry::instance();
$reg_manager->setEntry( AMP_REGISTRY_SETTING_ENCODING, $encoding );
$reg_manager->setEntry( AMP_REGISTRY_SETTING_SITENAME, $SystemSettings['websitename'] );
$reg_manager->setEntry( AMP_REGISTRY_SETTING_SITEURL, $SystemSettings['basepath'] );
$reg_manager->setEntry( AMP_REGISTRY_SETTING_EMAIL_SENDER, $SystemSettings['emfrom'] );
$reg_manager->setEntry( AMP_REGISTRY_SETTING_EMAIL_SYSADMIN, $SystemSettings['emfaq'] );
$reg_manager->setEntry( AMP_REGISTRY_SETTING_METADESCRIPTION, $SystemSettings['metadescription'] );
$reg_manager->setEntry( AMP_REGISTRY_SETTING_METACONTENT, $SystemSettings['metacontent'] );
$reg_manager->setEntry( AMP_REGISTRY_CONTENT_TEMPLATE_ID_DEFAULT, $SystemSettings['template'] );
define('AMP_SITE_URL', $SystemSettings['basepath']);
define('AMP_SITE_NAME', $SystemSettings['websitename']);
define('AMP_SITE_META_DESCRIPTION', $SystemSettings['metadescription']);

		
#SET DATABASE CACHING
$dbcon->cacheSecs = $cacheSecs;

$MM_USERNAME = AMP_DB_USER;
$MM_HOSTNAME = AMP_DB_HOST;
$MM_PASSWORD = AMP_DB_PASS;
$MM_DATABASE = AMP_DB_NAME;

define( "AMP_SYSTEM_VERSION_ID", '3.5.1');

#define browser detection global variables
$browser_ie = NULL;
$browser_win = NULL;
$browser_mo = NULL;
$browser_checked = false;

define('ADODB_REPLACE_INSERTED', 2);
define('ADODB_REPLACE_UPDATED', 1);
define('AMP_NAV_NO_LIMIT', 700);

?>
