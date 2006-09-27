<?php

require_once( 'Modules/WebAction/Target/ComponentMap.inc.php');

$map = &new ComponentMap_WebAction_Target( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
