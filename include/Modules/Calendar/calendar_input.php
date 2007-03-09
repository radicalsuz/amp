<?php

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Content/Map.inc.php' );
require_once( 'AMP/Content/Page.inc.php' );
require_once( 'Modules/Calendar/Public/ComponentMap.inc.php' );

$map = &new ComponentMap_Calendar_Public( );
$controller = &$map->get_controller( );

/**
 *  Initialize the Page
 */

$currentPage = &AMPContent_Page::instance();
if ( !isset( $_REQUEST['action']) && !isset( $_POST['submitAction'])) {
    $controller->request( 'add' );
}

$controller->set_page( $currentPage );
AMP_directDisplay( $controller->execute( ));

require_once("AMP/BaseFooter.php");

?>
