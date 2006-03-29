<?php

require_once( 'AMP/Content/Class/ComponentMap.inc.php');

$map = &new ComponentMap_Class( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
