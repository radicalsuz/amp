<?php

require_once( 'AMP/System/File/ComponentMap.inc.php');
$map = &new ComponentMap_File( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
