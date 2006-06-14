<?php

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Content/Map.inc.php' );
require_once( 'AMP/Content/Page.inc.php' );
require_once( 'AMP/Content/Article/Public/ComponentMap.inc.php' );

$map = &new ComponentMap_Article_Public( );
$controller = &$map->get_controller( );

/**
 *  Initialize the Page
 */

$currentPage = &AMPContent_Page::instance();
$controller->set_page( $currentPage );
AMP_directDisplay( $controller->execute( ));

require_once("AMP/BaseFooter.php");

?>
