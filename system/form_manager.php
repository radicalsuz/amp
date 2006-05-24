<?php

require_once( 'AMP/System/UserData/ComponentMap.inc.php');

$map = &new ComponentMap_AMP_System_UserData( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
