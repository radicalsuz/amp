<?php

//load sysvar table data
require_once('AMP/Registry.php');
require_once( 'AMP/System/Setup/Setup.php');
$system_setup_key = 'SYSTEM_SETUP_' . AMP_SYSTEM_UNIQUE_ID;

#if ( !( $SystemSetup = AMP_cache_get( $system_setup_key ))) {
    $SystemSetup = & new AMP_System_Setup( AMP_Registry::getDbcon( ));
#    AMP_cache_set( $system_setup_key, $SystemSetup );
#}
$SystemSetup->execute( );

$dbcon = &AMP_Registry::getDbcon( );
$dbcon->cacheSecs = 0;
if ( ( $cache = &AMP_get_cache( )) && ( strtolower( get_class( $cache )) == 'amp_system_cache_memcache')) {
    trigger_error( 'setting adodb/memcache to active' );
    $dbcon->memcache = true;
} else {
#    $dbcon->cacheSecs = $SystemSetup->getCacheTimeout( );
#    trigger_error( 'setting cache to ' . $dbcon->cacheSecs);
}

?>
