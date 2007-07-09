<?php

require_once( 'AMP/Content/Nav/Location/ComponentMap.inc.php');

$map = new ComponentMap_Nav_Location( );
$controller = $map->get_controller( );
print $controller->execute( );


?>
