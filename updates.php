<?php
if ( !defined( 'AMP_TOOL_INTROTEXT_UPDATES_CONTENT')) define( 'AMP_TOOL_INTROTEXT_UPDATES_CONTENT', 64 );

$intro_id = AMP_TOOL_INTROTEXT_UPDATES_CONTENT;

require_once( 'AMP/BaseTemplate.php' );
require_once( 'AMP/Content/Article/SetDisplay.inc.php' );

$contentPage = &AMPContent_Page::instance();
$filter = ( isset( $_GET['filter']) && $_GET['filter'])? $_GET['filter'] : false;

$articleSet = &new ArticleSet( &$dbcon );
$articleSet->addSortNewestFirst( );

$display = &new ArticleSet_Display( $articleSet );
if ( $filter ) $articleSet->addFilter( $filter );

$contentPage->contentManager->addDisplay( $display ) ;

require_once ( 'AMP/BaseFooter.php');

?>
