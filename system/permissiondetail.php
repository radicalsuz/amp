<?php

require_once( 'AMP/System/Permission/Detail/ComponentMap.inc.php');

$map = &new ComponentMap_PermissionDetail( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
