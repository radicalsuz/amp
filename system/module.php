<?php

require_once( 'AMP/System/Tool/ComponentMap.inc.php');

$map = &new ComponentMap_Tool( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
