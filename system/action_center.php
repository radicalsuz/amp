<?php

require_once( 'Modules/WebAction/ComponentMap.inc.php');

$map = &new ComponentMap_WebAction( );
$controller = &$map->get_controller( );

print $controller->execute( );

?>
