<?php

require_once( 'AMP/System/User/ComponentMap.inc.php');

$map = &new ComponentMap_User( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
