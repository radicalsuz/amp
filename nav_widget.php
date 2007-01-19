<?php

require_once ('AMP/BaseDB.php');

if ( $cached_output = AMP_cached_request( )) {
    print $cached_output;
    exit;
}

$intro_id = ( isset( $_GET['intro_id']) && $_GET['intro_id'] ) ? $_GET['intro_id'] : false;
$position = ( isset( $_GET['position']) && $_GET['position'] ) ? $_GET['position'] : false;

if ( !$position ) {
    trigger_error( 'no position requested for ' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
    exit;
}


require_once ('AMP/BaseTemplate.php');
$current_page = &AMPContent_Page::instance( );

require_once('AMP/Content/Template.inc.php' );
require_once('AMP/Content/Nav/Manager.inc.php' );

$template = & new AMPContent_Template( AMP_Registry::getDbcon( ), $current_page->getTemplateId( ));
if (!$template->hasData()) return false;

$template->setPage( $current_page );
$template->globalizeNavLayout();
$nav_manager = &new NavigationManager( $template, $current_page );
$finalPageHtml = $nav_manager->output( strtoupper( substr( $position, 0, 1 )));
print $finalPageHtml;

if ( AMP_is_cacheable_url( ) ) {
    $cache_key = AMP_CACHE_TOKEN_URL_CONTENT . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $user_id =  ( defined( 'AMP_SYSTEM_USER_ID' ) && AMP_SYSTEM_USER_ID ) ? AMP_SYSTEM_USER_ID : null; 
    AMP_cache_set( $cache_key, $finalPageHtml, $user_id );
}
?>
