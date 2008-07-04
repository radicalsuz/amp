<?php

require_once( 'AMP/Base/Config.php' );
require_once( 'AMP/Content/Map.inc.php' );
require_once( 'AMP/Content/Page.inc.php' );
require_once( 'AMP/Content/Article/Comment/Public/ComponentMap.inc.php' );

$modid = AMP_MODULE_ID_COMMENTS;

$map = &new ComponentMap_Article_Comment_Public( );
$controller = &$map->get_controller( );

/**
 *  Initialize the Page
 */

$currentPage = &AMPContent_Page::instance();
$controller->set_page( $currentPage );
AMP_directDisplay( $controller->execute( ));

require_once("AMP/BaseFooter.php");

?>
