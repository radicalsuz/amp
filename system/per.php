<?php

require_once( 'AMP/System/Permission/Group/ComponentMap.inc.php');
require_once( 'AMP/System/Page.inc.php');

$map = &new ComponentMap_PermissionGroup( );
$controller = &$map->get_controller( );

print $controller->execute( );

?>
