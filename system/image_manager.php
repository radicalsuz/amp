<?php

require_once( 'AMP/System/File/Image/ComponentMap.inc.php');
$map = &new ComponentMap_Image( );
$controller = &$map->get_controller( );
$modid = AMP_MODULE_ID_GALLERY;

print $controller->execute( );

/*
require_once( 'Modules/Gallery/Image/ComponentMap.inc.php');
require_once( 'AMP/Content/Image/List.inc.php');
require_once( 'AMP/System/Page.inc.php');
$modid = AMP_MODULE_ID_GALLERY;

$page = &AMPSystem_Page::instance( );
$page->setComponentName( 'AMP_Content_Image_List', 'list');
$page->showList( true );

$display = &$page->getDisplay( );
$display->setItemType( 'Image');
$display->setNavName( 'gallery');

print $page->output( );
*/
?>
