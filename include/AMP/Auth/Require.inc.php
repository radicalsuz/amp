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

print $_SYSTEM['DOCUMENT_ROOT'];

require_once( 'AMP/Auth/Handler.inc.php' );
require_once( 'AMP/BaseDB.php' );

$AMP_Authen_Handler = new AMP_Authentication_Handler( $dbcon );

if ( !$AMP_Authen_Handler->is_authenticated() )
    $AMP_Authen_Handler->do_login();

ob_end_flush();

?>
