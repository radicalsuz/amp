<?php

$base_path = AMP_BASE_PATH;

// Not Quite Sure what these do.
$MX_type = "type";
$MX_top = AMP_CONTENT_MAP_ROOT_SECTION;

$PHP_SELF = $_SERVER['PHP_SELF'];

define( 'MAGIC_QUOTES_ACTIVE', get_magic_quotes_gpc());

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
$cacheSecs           = $SystemSettings["cacheSecs"];
$admEmail            = $SystemSettings["emfaq"];			//needed for admin only
$MM_email_usersubmit = $SystemSettings["emendorse"];			//User Submitted Article
$MM_email_from       = $SystemSettings["emfrom"];			//return email web sent emails
$meta_description    = $SystemSettings["metadescription"];		//meta desc
$meta_content        = $SystemSettings['metacontent'];			//meta content
$systemplate_id      = $SystemSettings['template'];

define ('AMP_SITE_ADMIN', $admEmail);
define ('AMP_SYSTEM_BLAST_EMAIL_SENDER', $SystemSettings['emmedia'] );
define ('AMP_SYSTEM_BLAST_EMAIL_SENDER_NAME', $SystemSettings['emailfromname'] );
define ('AMP_SITE_EMAIL_SENDER', $SystemSettings['emfrom'] );

$encoding = isset($SystemSettings['encoding']) ? $SystemSettings['encoding'] : 'iso-8859-1';

define('AMP_CONTENT_TEMPLATE_ID_DEFAULT', $SystemSettings['template'] );
define('AMP_SITE_CONTENT_ENCODING', $encoding );
define('AMP_SITE_URL', $SystemSettings['basepath']);
define('AMP_SITE_NAME', $SystemSettings['websitename']);
define('AMP_SITE_META_DESCRIPTION', $SystemSettings['metadescription']);
define('AMP_SITE_META_KEYWORDS', $SystemSettings['metacontent']);
define('AMP_SITE_CACHE_TIMEOUT', $cacheSecs );

define('AMP_IMAGE_WIDTH_THUMB', $SystemSettings[ 'thumb' ] );
define('AMP_IMAGE_WIDTH_TALL', $SystemSettings[ 'optl' ] );
define('AMP_IMAGE_WIDTH_WIDE', $SystemSettings[ 'optw' ] );

#SET DATABASE CACHING
$dbcon->cacheSecs = $cacheSecs;

$MM_USERNAME = AMP_DB_USER;
$MM_HOSTNAME = AMP_DB_HOST;
$MM_PASSWORD = AMP_DB_PASS;
$MM_DATABASE = AMP_DB_NAME;

define( "AMP_SYSTEM_VERSION_ID", '3.5.4');

#define browser detection global variables
$browser_ie = NULL;
$browser_win = NULL;
$browser_mo = NULL;
$browser_checked = false;

define('ADODB_REPLACE_INSERTED', 2);
define('ADODB_REPLACE_UPDATED', 1);
define('AMP_NAV_NO_LIMIT', 700);
define('AMP_ARTICLE_ALLOW_MULTIPLE_SECTIONS', (isset($MM_reltype)&&$MM_reltype) );
define('AMP_NULL_DATE_VALUE', '0000-00-00' );
define('AMP_DISPLAYMODE_DEBUG', (isset($_GET['debug']) && $_GET['debug']));
define('AMP_DISPLAYMODE_DEBUG_PLUGINS', (isset($_GET['debug_plugins']) && $_GET['debug_plugins']));
define('AMP_DISPLAYMODE_DEBUG_LOOKUPS', (isset($_GET['debug_lookups']) && $_GET['debug_lookups']));
define('AMP_DISPLAYMODE_DEBUG_NAVS', (isset($_GET['debug_navs']) && $_GET['debug_navs']));
define( 'AMP_TEXT_LOGIN_HELP_ADMIN', 'If you are having trouble logging in, please contact the <a href="mailto:%s">site administrator</a>.' );

if (!defined( 'AMP_CONTENT_INTRO_ID_DEFAULT' )) define( 'AMP_CONTENT_INTRO_ID_DEFAULT' , 1 );
if (!defined( 'AMP_DBTABLE_BLAST_LISTS')) define ( 'AMP_DBTABLE_BLAST_LISTS', false );

?>
