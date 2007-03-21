<?php

require_once( 'AMP/System/Permission/Group/ComponentMap.inc.php');

$map = &new ComponentMap_PermissionGroup( );
$controller = &$map->get_controller( );

print $controller->execute( );

?>
