<?php

require_once( 'AMP/Content/Article/ComponentMap.inc.php' );

$map = &new ComponentMap_Article( );
$controller = &$map->get_controller( );
$controller->request( 'view' );
print $controller->execute( );

/*
require_once( 'AMP/System/Page/Content.inc.php' );
require_once( 'AMP/Content/Article/ComponentMap.inc.php' );

$map = &new ComponentMap_Article();
$page = &new AMPSystem_Page_Content( $dbcon, $map );
$page->addComponent('search');
$page->default_action = "menu";
$page->addComponentHeader( 'menu', 'Site Map' );
$page->addComponent('classlinks');

$page->execute();

$page->orderComponents(  array('search', 'list', 'menu', 'classlinks') );

print $page->output();
*/

?>
