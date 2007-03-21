<?php

require_once( 'AMP/Content/Badge/ComponentMap.inc.php');

$map = &new ComponentMap_Badge( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
