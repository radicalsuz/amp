<?php

require_once( 'AMP/BaseDB.php');

$request_key = ( isset( $_GET['key']) && $_GET['key'] ) ? $_GET['key'] : false;
if ( !$request_key ) exit;
if ( !( $cache = &AMP_get_cache( ))) exit;

$authorized_key = false;
if ( defined( 'AMP_SYSTEM_USER_ID') && AMP_SYSTEM_USER_ID ){
    $authorized_key = $cache->identify( $request_key, AMP_SYSTEM_USER_ID );
}

if ( $content_type = $cache->header( $request_key )) {

    //should send an appropriate header depending on the key type
    header( $content_type );

}

$active_key = ( $cache->contains( $authorized_key )) ? $authorized_key : $request_key;
if ( !$active_key ) exit;

//$result = $cache->retrieve( $authorized_key );
//print $result;
print $cache->retrieve( $active_key );

?>
