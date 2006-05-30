<?php

require_once( 'AMP/System/Setup/Wizard/ComponentMap.inc.php');

$map = &new ComponentMap_Setup_Wizard( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
