<?php

require_once( 'AMP/System/CustomFile/ComponentMap.inc.php');

$map = &new ComponentMap_CustomFile( );

$controller = & $map->get_controller( );
print $controller->execute( );

?>
