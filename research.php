<?php
if ( !defined( 'AMP_TOOL_INTROTEXT_SEARCH_CONTENT')) define( 'AMP_TOOL_INTROTEXT_SEARCH_CONTENT', 62 );

$intro_id = AMP_TOOL_INTROTEXT_SEARCH_CONTENT;

require_once( 'AMP/BaseTemplate.php');
require_once( 'AMP/Content/Article/Search/User/Display.inc.php');

$contentPage = &AMPContent_Page::instance();
$filter = ( isset( $_GET['filter']) && $_GET['filter'])? $_GET['filter'] : false;

$display = &new ContentSearch_Display_User();
if ( $filter ) $display->addFilter( $filter );

$contentPage->contentManager->addDisplay( $display ) ;

require_once ( 'AMP/BaseFooter.php');

?>
