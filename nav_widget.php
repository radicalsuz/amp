<?php

require_once ('AMP/BaseDB.php');

if ( $cached_output = AMP_cached_request( )) {
    print $cached_output;
    exit;
}

$intro_id = ( isset( $_GET['intro_id']) && $_GET['intro_id'] ) ? intval( $_GET['intro_id'] ) : false;
$position = ( isset( $_GET['position']) && $_GET['position'] ) ? $_GET['position'] : false;
$format = ( isset( $_GET['format']) && $_GET['format'] ) ? $_GET['format'] : false;

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

$header = &AMP_get_header( );
$standard_js = $header->_HTML_javaScripts( );

$template->setPage( $current_page );
$template->globalizeNavLayout();
$nav_manager = &new NavigationManager( $template, $current_page );
$nav_output = $nav_manager->output( strtoupper( substr( $position, 0, 1 )));

$new_js = $header->_HTML_javascripts( );
$nav_js = array_diff( split( "\n", $new_js), split( "\n", $standard_js ));
$nav_output = $nav_output.join( "\n",$nav_js );

/*
$url = AMP_SITE_URL;

$pattern = '/href\s?=\s?\'((?!http)[\w\d\.\/?=& -]*)\'/i';
$replace = 'href="'.$url.'/$1"';
$data =  preg_replace($pattern, $replace, $nav_output);

$pattern = '/href\s?=\s?"((?!http)[\w\d\.\/?=& -]*)"/i';
$replace = 'href="'.$url.'/$1"';
$data =  preg_replace($pattern, $replace, $data);

$pattern = '/src\s?=\s?"((?!http)[\w\d\.\/?=& -]*)"/i';
$replace = 'src="'.$url.'/$1"';
$data =  preg_replace($pattern, $replace, $data);

$pattern = '/src\s?=\s?\'((?!http)[\w\d\.\/?=& -]*)\'/i';
$replace = 'src="'.$url.'/$1"';
$data =  preg_replace($pattern, $replace, $data);

$pattern = '/background\s?=\s?"((?!http)[\w\d\.\/?=& -]*)"/i';
$replace = 'background="'.$url.'/$1"';
$data =  preg_replace($pattern, $replace, $data);

$pattern = '/action\s?=\s?"((?!http)[\w\d\.\/?=& -]*)"/i';
$replace = 'action="'.$url.'/$1"';
$data =  preg_replace($pattern, $replace, $data);

$pattern = '/,\'\',\'((?!http)[\w\d\.\/?=& -]*)\'/i';
$replace = ',\'\',\''.$url.'/$1\'';
$data =  preg_replace($pattern, $replace, $data);

$pattern = array( "\r", "\n" );
$finalPageHtml =  str_replace($pattern, '', $data);
*/

$finalPageHtml = AMP_absolute_urls( $nav_output );

if ( $format == 'js' ) {
    $nav_id = $position . mt_rand( 1000, 10000 );
    $finalPageHtml = AMP_js_write( $finalPageHtml, $nav_id );
    //$finalPageHtml = 'var '.$nav_id.'=  { value: \''. str_replace( "'", "\'", $finalPageHtml ) . "'};\ndocument.write( ".$nav_id.".value );";

}
print $finalPageHtml;

if ( AMP_is_cacheable_url( ) ) {
    $cache_key = AMP_CACHE_TOKEN_URL_CONTENT . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $user_id =  ( defined( 'AMP_SYSTEM_USER_ID' ) && AMP_SYSTEM_USER_ID ) ? AMP_SYSTEM_USER_ID : null; 
    AMP_cache_set( $cache_key, $finalPageHtml, $user_id );
}
?>
