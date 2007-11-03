<?php

require_once( 'Modules/Rating/ComponentMap.inc.php');

$map = &new ComponentMap_Rating( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
