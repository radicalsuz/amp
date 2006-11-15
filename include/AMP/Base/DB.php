<?php

require_once('utility.system.functions.inc.php');
AMP_init_local_path( );

require_once('utility.functions.inc.php');

if (!isset( $ADODB_CACHE_DIR ) || !is_dir($ADODB_CACHE_DIR) || !is_writable($ADODB_CACHE_DIR)){
	$ADODB_CACHE_DIR = AMP_urlFlip( AMP_LOCAL_PATH . '/cache' );
}

require_once('adodb/adodb.inc.php');


// Look for a local site configuration.
if (file_exists_incpath( 'SiteConfig.php' )) {

    // This form is preferred.
    require('SiteConfig.php');

    if ( !defined('AMP_DB_HOST') ||
            !defined('AMP_DB_USER') ||
            !defined('AMP_DB_PASS') ||
            !defined('AMP_DB_NAME') ) {

        die( "Incomplete site configuration. Please contact your system administrator." );

    }


} elseif (file_exists_incpath( 'config.php' )) {
    if (!defined( 'AMP_BASE_PATH' ))        define( 'AMP_BASE_PATH', $_SERVER['DOCUMENT_ROOT'] );

    // Included for backwards-compatibility.
    require_once('config.php');

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

if (!isset( $ADODB_CACHE_DIR ) || !is_dir($ADODB_CACHE_DIR) || !is_writable($ADODB_CACHE_DIR)){
	$ADODB_CACHE_DIR = AMP_urlFlip( AMP_LOCAL_PATH . '/cache' );
}

if (!defined( 'AMP_BASE_PATH' ))        define( 'AMP_BASE_PATH', $_SERVER['DOCUMENT_ROOT'] );
if (!defined( 'AMP_BASE_INCLUDE_PATH')) define( 'AMP_BASE_INCLUDE_PATH', $_SERVER['DOCUMENT_ROOT'].'/include/' );

// turn on APD debugger when set by config file
if ( defined( 'AMP_DEBUG_MODE_APD' ) && AMP_DEBUG_MODE_APD ) apd_set_pprof_trace(); 

// Connect to the database.
if (!defined('AMP_DB_TYPE'))
    define('AMP_DB_TYPE', 'mysql');


ADOLoadCode(AMP_DB_TYPE);

$dbcon =& ADONewConnection( AMP_DB_TYPE );
if (! $dbcon->Connect( AMP_DB_HOST, AMP_DB_USER, AMP_DB_PASS, AMP_DB_NAME )) {
    die( 'Connection to database '.AMP_DB_NAME.' was refused.  Please check your site configuration file.' );
}

require_once('AMP/Registry.php');
//add the dbcon to the Registry
$registry =& AMP_Registry::instance();
$registry->setDbcon($dbcon);

?>
