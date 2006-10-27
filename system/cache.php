<?php
include_once( 'AMP/System/Base.php');
if ( !( isset( $_POST['action']) && $_POST['action'] == 'flush')) {
    include( AMP_BASE_PATH . '/cache.php');
    exit;
}


header('Cache-Control: no-cache');
header('Pragma: no-cache');
trigger_error( sprintf( AMP_TEXT_CACHE_RESET_INTERNAL,  $_SERVER['HTTP_REFERER'], AMP_SYSTEM_USER_ID ));
AMP_cacheFlush( );
$flash = AMP_System_Flash::instance( );
$flash->add_message( AMP_TEXT_CACHE_RESET );
print $flash->execute( );

?>
