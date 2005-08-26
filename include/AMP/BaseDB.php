<?php

require_once('adodb/adodb.inc.php');
require_once('utility.functions.inc.php');

$ampdbcon = null;

// Check for a hosted configuration.
if (file_exists_incpath( 'AMP/HostConfig.inc.php' )) {

    // Hosted Mode Configuration Present, load configuration
    require_once('AMP/HostConfig.inc.php');

    if (AMP_HOSTED) {

        // Try to fetch configuration from a central service provider database.
        if ( !defined('AMP_DB_TYPE') ) define('AMP_DB_TYPE', 'mysql');
        ADOLoadCode( AMP_DB_TYPE );

        // If enabled, fetch information about our config.
        $ampdbcon = &ADONewConnection('mysql');
        $ampdbcon->Connect( AMP_DB_HOST, AMP_DB_USER, AMP_DB_PASS, AMP_DB_NAME );

        $AMPsql = "SELECT * FROM system WHERE server='".$_SERVER['SERVER_NAME']."'";

        $ampconfig = $ampdbcon->Execute( $AMPsql ) 
                    or die( "Couldn't fetch local configuration: " . $ampdbcon->ErrorMsg());

        if ( is_dir($ampconfig->Fields('amppath')) )
            define( 'AMP_LOCAL_PATH', $ampconfig->Fields('amppath') );

    }

    // Clean up constants for security.
    define('AMP_DB_HOST', null);
    define('AMP_DB_NAME', null);
    define('AMP_DB_USER', null);
    define('AMP_DB_PASS', null);
}

// If we don't yet have a local path, find one.
if (!defined('AMP_LOCAL_PATH'))
     define( 'AMP_LOCAL_PATH', find_local_path() );

if ( AMP_LOCAL_PATH ) {
	$incpath = ini_get('include_path');
    // Search local paths for includes.
    ini_set( 'include_path', AMP_LOCAL_PATH . "/custom" . PATH_SEPARATOR .
                             AMP_LOCAL_PATH . "/lib:" . PATH_SEPARATOR .
                             ini_get('include_path') );
}

// Look for a local site configuration.
if (file_exists_incpath( 'SiteConfig.inc.php' )) {

    // This form is preferred.
    require('SiteConfig.inc.php');

    if ( !defined('AMP_DB_HOST') ||
            !defined('AMP_DB_USER') ||
            !defined('AMP_DB_PASS') ||
            !defined('AMP_DB_NAME') ) {

        die( "Incomplete site configuration. Please contact your system administrator." );

    }


} elseif (file_exists_incpath( 'config.php' )) {
    if (!defined( 'AMP_BASE_PATH' )) define( 'AMP_BASE_PATH', $_SERVER['DOCUMENT_ROOT'] );

    // Included for backwards-compatibility.
    require('config.php');

    if (isset($MM_HOSTNAME) && isset($MM_USERNAME) && isset($MM_PASSWORD) && isset($MM_DATABASE)) {

        if (isset($MM_DBTYPE)) define('AMP_DB_TYPE', $MM_DBTYPE);
        define('AMP_DB_HOST', $MM_HOSTNAME);
        define('AMP_DB_USER', $MM_USERNAME);
        define('AMP_DB_PASS', $MM_PASSWORD);
        define('AMP_DB_NAME', $MM_DATABASE);

    } else {
        die( "Incomplete database configuration. Please contact your system administrator." );
    }

} else {

    die( "Couldn't find a local site configuration file. Please contact your system administrator." );
}
if (!defined( 'AMP_BASE_INCLUDE_PATH' )) define( 'AMP_BASE_INCLUDE_PATH', $_SERVER['DOCUMENT_ROOT'].'/include/' );

// Connect to the database.
if (!defined('AMP_DB_TYPE'))
    define('AMP_DB_TYPE', 'mysql');

if (!is_dir($ADODB_CACHE_DIR) || !is_writable($ADODB_CACHE_DIR))
        $ADODB_CACHE_DIR = AMP_LOCAL_PATH . '/cache';

ADOLoadCode(AMP_DB_TYPE);

$dbcon =& ADONewConnection( AMP_DB_TYPE );
$dbcon->Connect( AMP_DB_HOST, AMP_DB_USER, AMP_DB_PASS, AMP_DB_NAME );

if (!defined( 'AMP_SITE_MEMCACHE_ON' ))       define ('AMP_SITE_MEMCACHE_ON', true);

require_once('AMP/Registry.php');
if (AMP_SITE_MEMCACHE_ON) require_once('AMP/System/Memcache.inc.php');
require_once('AMP/System/Lookups.inc.php');
$registry =& AMP_Registry::instance();
$registry->setDbcon($dbcon);
$lookup_factory = & AMPSystem_LookupFactory::instance();
$lookup_factory->init( $dbcon );

require_once('AMP/LegacyRegistry.inc.php');


if (!defined( 'AMP_CONTENT_MAP_ROOT_SECTION' )) define( 'AMP_CONTENT_MAP_ROOT_SECTION' , 1 );
if (!defined( 'AMP_DEBUG_MODE_APD')) define ('AMP_DEBUG_MODE_APD', false );
if ( AMP_DEBUG_MODE_APD ) apd_set_pprof_trace(); 
?>
