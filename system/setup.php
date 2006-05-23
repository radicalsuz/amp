<?php

require_once( 'AMP/System/Setup/ComponentMap.inc.php');

$map = &new ComponentMap_Setup( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
