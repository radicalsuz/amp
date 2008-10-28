<?php

$request = ( isset( $_GET['q_url'] ) && $_GET['q_url'] ) ? $_GET['q_url'] : false;
require_once( 'AMP/Base/Config.php');
require_once( 'AMP/Dispatcher.php' );

if ( $cached_output = AMP_cached_request( )) {
    print $cached_output;
    exit;
}

if( $request && ( $controller = AMP_Dispatcher::respond( $request ))) {
    $controller->execute( );
} else {
    AMP_make_404();
    exit;
}
require_once("AMP/BaseFooter.php");

//add the section header
/*
$content->add( $display ); 
 */

/*
if( $request ) $route = AMP_dispatch_for( $request );
if( !( $request && $route )) {
  AMP_make_404();
  exit;
}

$target_class = ucfirst( $route['target_type']);
$target = new $target_class( AMP_dbcon(), $route['target_id'] );

if( !( $target && $target->hasData() && ( $target->isDisplayable( ) || AMP_Authenticate( 'admin') ) )) AMP_make_404();

$display = $target->getDisplay();

$currentPage = & AMPContent_Page::instance();
$content = & $currentPage->contentManager;

$page_method = 'set' . $target_class;
$currentPage->$page_method( $route['target_id'] );
$currentPage->initLocation();
 */


?>
