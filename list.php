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

$tag_header = ( isset( $_REQUEST['tag_header']) && $_REQUEST['tag_header']) ? $_REQUEST['tag_header'] : false;
$tag = ( isset( $_REQUEST['tag']) && $_REQUEST['tag']) ? $_REQUEST['tag'] : false;
if( $tag_header && $tag ) {
    require_once( 'AMP/Content/Tag/Public/Search/Description.php');

    $tag_intro = new AMP_Content_Tag_Public_Search_Description( $tag );
    $page->contentManager->add( $tag_intro, AMP_CONTENT_DISPLAY_KEY_INTRO );
}

$map = &new ComponentMap_Article_Public();
$map->use_prefix( 'content', 'search_fields' );
$list =  $map->getComponent( 'content_list');

//$list = new Article_Public_List();
$list->suppress( 'search_form' );
$page->contentManager->add( $list );

require( 'AMP/BaseFooter.php');

?>
