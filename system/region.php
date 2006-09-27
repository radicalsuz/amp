<?php

require_once( 'AMP/System/Region/ComponentMap.inc.php' );

$map = &new ComponentMap_Region();
$controller = &$map->get_controller( );
print $controller->execute( );

?>
