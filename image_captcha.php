<?php
$key = ( isset( $_GET['key']) && $_GET['key'] ) ? $_GET[ 'key' ] : false;
if ( $key ) $_COOKIE['AMP_SYSTEM_UNIQUE_VISITOR_ID'] = $key; 

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Form/Element/Captcha.inc.php');

$captcha_fonts = array( 
    AMP_BASE_INCLUDE_PATH . 'TrueType/FreeSans.ttf',
    AMP_BASE_INCLUDE_PATH . 'TrueType/FreeMono.ttf',
    AMP_BASE_INCLUDE_PATH . 'TrueType/FreeSerif.ttf'
    );
$captcha = &new PhpCaptcha( $captcha_fonts );

$captcha->Create( );

?>
