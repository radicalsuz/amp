<?php

require_once( 'Modules/Housing/ComponentMap.inc.php');

$map = &new ComponentMap_Housing( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
