<?php
$key = ( isset( $_GET['key']) && $_GET['key'] ) ? $_GET[ 'key' ] : false;
$format = ( isset( $_GET['format']) && $_GET['format'] ) ? $_GET[ 'format' ] : false;
if ( $key ) $_COOKIE['AMP_SYSTEM_UNIQUE_VISITOR_ID'] = $key; 

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Form/Element/Captcha.inc.php');

header('Cache-Control: no-cache');
header('Pragma: no-cache');

if ( $format == 'audio') {
    $captcha = &new AudioPhpCaptcha( );


} else {
    $captcha_fonts = array( 
        //AMP_BASE_INCLUDE_PATH . 'TrueType/FreeSans.ttf',
        //AMP_BASE_INCLUDE_PATH . 'TrueType/FreeMono.ttf',
        //AMP_BASE_INCLUDE_PATH . 'TrueType/FreeSerif.ttf',
        AMP_BASE_INCLUDE_PATH . 'TrueType/handfont2.ttf',
        AMP_BASE_INCLUDE_PATH . 'TrueType/Tuffy.ttf',
        AMP_BASE_INCLUDE_PATH . 'TrueType/Tribal_Font.ttf'
        );
    $captcha = &new PhpCaptcha( $captcha_fonts );

}

$captcha->Create( );

?>
