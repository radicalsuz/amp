<?php
if ( !defined( 'AMP_TOOL_INTROTEXT_SEARCH_CONTENT')) define( 'AMP_TOOL_INTROTEXT_SEARCH_CONTENT', 62 );

$intro_id = AMP_TOOL_INTROTEXT_SEARCH_CONTENT;

require_once( 'AMP/BaseTemplate.php');
require_once( 'AMP/Content/Article/Search/User/Display.inc.php');

$contentPage = &AMPContent_Page::instance();
$contentPage->contentManager->addDisplay( new ContentSearch_Display_User() );

require_once ( 'AMP/BaseFooter.php');

?>
