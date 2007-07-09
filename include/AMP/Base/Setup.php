<?php

//load sysvar table data
require_once('AMP/Registry.php');
require_once( 'AMP/System/Setup/Setup.php');

//initialize System Setup
/*
require_once( 'AMP/System/Lookups.inc.php');
$sites = AMP_lookup( 'subsites');
$server_name = $_SERVER['SERVER_NAME'];
foreach( $sites as $site_id => $site_url ) {
    if ( strpos( $site_url, $_SERVER['SERVER_NAME'])) {
        define( 'AMP_SYSTEM_SETTING_DB_ID', $site_id );
        trigger_error( 'found site id ' . $site_id );
    }
}
if ( !defined( 'AMP_SYSTEM_SETTING_DB_ID')) {
    define( 'AMP_SYSTEM_SETTING_DB_ID', AMP_CONTENT_SECTION_ID_ROOT );
}
*/

//check cache
$system_setup_key = 'SYSTEM_SETUP_' . AMP_SYSTEM_UNIQUE_ID . '_' . AMP_SYSTEM_SETTING_DB_ID;
if ( !( $SystemSetup = AMP_cache_get( $system_setup_key ))) {
    $SystemSetup = & new AMP_System_Setup( AMP_Registry::getDbcon( ));
    AMP_cache_set( $system_setup_key, $SystemSetup );
}
$SystemSetup->execute( );

// enable memcache for adodb if memcache is active
if ( ( $cache = &AMP_get_cache( )) && ( strtolower( get_class( $cache )) == 'amp_system_cache_memcache')) {
    $dbcon = &AMP_Registry::getDbcon( );
    $dbcon->memcache = true;
    $dbcon->cacheSecs = AMP_SITE_CACHE_TIMEOUT; 
} 

?>
