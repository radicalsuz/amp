<?php

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Content/Map.inc.php' );
require_once( 'AMP/Content/Page.inc.php' );
require_once( 'AMP/Content/Article/Public/ComponentMap.inc.php' );

$map = &new ComponentMap_Article_Public( );
$map->use_prefix( AMP_CONTENT_ARTICLE_SEARCH_MAP_LIST_PREFIX, 'list' );
$controller = &$map->get_controller( );
$controller->request('search');

/**
 *  Initialize the Page
 */

$currentPage = &AMPContent_Page::instance();
$controller->set_page( $currentPage );
AMP_directDisplay( $controller->execute( ));

require_once("AMP/BaseFooter.php");

?>
