<?php

require_once( 'AMP/Base/Config.php');

if ( $cached_output = AMP_cached_request( )) {
    print $cached_output;
    exit;
}

require_once( 'AMP/Content/Article/Public/ComponentMap.inc.php');
require_once( 'AMP/Content/Page.inc.php');

$_REQUEST['action'] = 'search';
$page = &AMPContent_Page::instance( );
$template_section = ( isset( $_REQUEST['template_section']) && $_REQUEST['template_section'] ) ? $_REQUEST['template_section'] : false;
if (!$template_section) {
	$template_section = ( isset( $_REQUEST['section']) && $_REQUEST['section'] && is_numeric($_REQUEST['section']) ) ? $_REQUEST['section'] : AMP_CONTENT_SECTION_ID_ROOT;
}
$page->setSection( $template_section );

$map = new ComponentMap_Article_Public();
$map->use_search( 'content' );
$list =  $map->getComponent( 'content_list');

//$list = new Article_Public_List();
$list->suppress( 'search_form' );
$page->contentManager->add( $list );

require( 'AMP/BaseFooter.php');

?>
