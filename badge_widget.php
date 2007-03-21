<?php
require_once ('AMP/BaseDB.php');

if ( $cached_output = AMP_cached_request( )) {
    print $cached_output;
    exit;
}

$badge_id = ( isset( $_GET['id']) && $_GET['id'] ) ? $_GET['id'] : false;
if (!$badge_id) $badge_id = ( isset( $_GET['badge']) && $_GET['badge'] ) ? $_GET['badge'] : false;
$format = ( isset( $_GET['format']) && $_GET['format'] ) ? $_GET['format'] : false;

if ( !$badge_id ) {
    trigger_error( 'no badge requested for ' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
    exit;
}

require_once ('AMP/Content/Badge/ComponentMap.inc.php');
$map = new ComponentMap_Badge();
$badge = $map->getComponent('source');
$badge->readData( $badge_id );
if (!($badge->hasData() && $badge->isLive())) {
	trigger_error( AMP_TEXT_ERROR_OPEN_FAILED, 'Badge '.$badge_id ); 
	exit;
}

$result = $badge->execute();


$finalPageHtml = AMP_absolute_urls( $result );

if ( $format != 'xml' ) {
    $finalPageHtml = AMP_js_write( $finalPageHtml );

}

print $finalPageHtml;

if ( AMP_is_cacheable_url( ) ) {
    $cache_key = AMP_CACHE_TOKEN_URL_CONTENT . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $user_id =  ( defined( 'AMP_SYSTEM_USER_ID' ) && AMP_SYSTEM_USER_ID ) ? AMP_SYSTEM_USER_ID : null; 
    AMP_cache_set( $cache_key, $finalPageHtml, $user_id );
}

?>
