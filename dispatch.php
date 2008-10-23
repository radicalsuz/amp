<?php

$request = ( isset( $_GET['q_url'] ) && $_GET['q_url'] ) ? $_GET['q_url'] : false;
require_once( 'AMP/Base/Config.php');
require_once( 'AMP/Content/Page.inc.php' );

if(!$request) { 
  AMP_redirect( AMP_CONTENT_URL_FRONTPAGE ) ;
};

$route = AMP_dispatch_for( $request );
if( !$route ) {
  AMP_make_404();
  exit;
}

$target_class = ucfirst( $route['target_type']);
$target = new $target_class( AMP_dbcon(), $route['target_id'] );

if( !( $target && $target->hasData() )) AMP_make_404();
$display = $target->getDisplay();

$currentPage = & AMPContent_Page::instance();
$page_method = 'set' . $target_class;
$currentPage->$page_method( $route['target_id'] );
$content = $currentPage->contentManager;
$intro_id = AMP_CONTENT_INTRO_ID_DEFAULT;
$currentPage->initLocation();

if( !isset( $display->pager ) || $display->pager->is_first_page( )) {
    if( method_exists( $display, 'render_intro')) {
        require_once( 'AMP/Content/Buffer.php');
        $intro = new AMP_Content_Buffer( );
        $intro->add( $display->render_intro( ));
    } else {
        $intro = $currentPage->getListDisplayIntro( );
    }
    if( $intro ) {
        $content->add( $intro, AMP_CONTENT_DISPLAY_KEY_INTRO );
    }
}

$content->add( $display ); 
print $currentPage->output();

?>
