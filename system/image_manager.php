<?php

require_once( 'AMP/System/File/Image/ComponentMap.inc.php');
$map = &new ComponentMap_Image( );
$controller = &$map->get_controller( );
$modid = AMP_MODULE_ID_GALLERY;

print $controller->execute( );

?>
