<?php

require_once( 'AMP/Base/Config.php');
require_once( 'AMP/Content/Article/Public/ComponentMap.inc.php');
require_once( 'AMP/Content/Page.inc.php');

$page = AMPContent_Page::instance( );
$template_section = ( isset( $_REQUEST['template_section']) && $_REQUEST['template_section'] ) ? $_REQUEST['template_section'] : AMP_CONTENT_SECTION_ID_ROOT;
$page->setSection( $template_section );

$map = new ComponentMap_Article_Public( );
$list = $map->getComponent( 'list');
$list->suppress( 'search_form' );
$page->contentManager->add( $list );
print $page->output( );

?>
