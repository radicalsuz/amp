<?php

/*****
 *
 * AMP Authentication Handler
 *
 * The inclusion of this file will require authentication to succeed before
 * allowing access to any pages. It provides username and password information
 * by setting the $_SYSTEM['REMOTE_USER'] and $_SYSTEM['REMOTE_GROUP']
 *
 *****/

ob_start();

require_once( 'AMP/Auth/Handler.inc.php' );

$AMP_Authen_Handler = &new AMP_Authentication_Handler( $dbcon );

if ( !$AMP_Authen_Handler->is_authenticated() ) {
    $AMP_Authen_Handler->do_login();
} else {
    define( 'AMP_USERMODE_ADMIN', true );
}
AMP_init_user( );
$AMP_Authen_Handler->redirect_page( );

if ( !empty($_POST) ) {
    require_once( 'AMP/System/Cache/Config.inc.php');
    AMP_cacheFlush( AMP_CACHE_TOKEN_URL_CONTENT );
    AMP_cacheFlush( AMP_CACHE_TOKEN_ADODB );
}

?>
