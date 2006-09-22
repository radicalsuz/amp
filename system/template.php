<?php

require_once( 'AMP/Content/Template/ComponentMap.inc.php');

$map = &new ComponentMap_Template( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
