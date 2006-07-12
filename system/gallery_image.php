<?php

require_once("Modules/Gallery/Image/ComponentMap.inc.php");

$modid = AMP_MODULE_ID_GALLERY;
$map = &new ComponentMap_GalleryImage();
$controller = &$map->get_controller( );

print $controller->execute( );

?>
