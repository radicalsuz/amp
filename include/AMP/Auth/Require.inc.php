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

//ob_start();

require_once( 'AMP/Auth/Handler.inc.php' );

$AMP_Authen_Handler = &new AMP_Authentication_Handler( $dbcon );

if ( ISSET( $_POST['AMPLogin_password']) && (( $_POST['AMPLogin_password']== 'changeme')||( $_POST['AMPLogin_password']== 'chang#th1s'))){
    $AMP_Authen_Handler->set_message( 'Default Passwords are no longer available.  Please contact <a style="color:#a5e2ff" href="mailto:help@radicaldesigns.org">help@radicaldesigns.org</a> for a new password.');
    $AMP_Authen_Handler->do_login( );
}

if ( !$AMP_Authen_Handler->is_authenticated() ) {
    $AMP_Authen_Handler->do_login();
} else {
    define( 'AMP_USERMODE_ADMIN', true );
}
AMP_init_user( );
$AMP_Authen_Handler->redirect_page( );

if ( !empty($_POST) ) {
    AMP_flush_common_cache( );
}

?>
