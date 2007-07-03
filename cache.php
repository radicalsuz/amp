<?php

require_once( 'AMP/BaseDB.php');

$request_key = ( isset( $_GET['key']) && $_GET['key'] ) ? $_GET['key'] : false;
if ( !$request_key ) {
    trigger_error( 'cache page called without key ' . $_SERVER['REQUEST_URI']);
    exit;
}
if ( !( $cache = &AMP_get_cache( ))) {
    trigger_error( 'cache page failed to load cache' );
    exit;
}

$authorized_key = $request_key;
if ( defined( 'AMP_SYSTEM_USER_ID') && AMP_SYSTEM_USER_ID ){
    $authorized_key = $cache->identify( $request_key, AMP_SYSTEM_USER_ID );
}

if ( $content_type = $cache->header( $request_key )) {

    //should send an appropriate header depending on the key type
    header( $content_type );

}

$content = $cache->retrieve( $authorized_key );
if ( !$content ) {
    trigger_error( 'item ' . $request_key . ' not found for user');
    exit;
}

print $content;

AMP_cache_close( );
?>
