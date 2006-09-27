<?php

require_once( 'AMP/Content/Nav/ComponentMap.inc.php');

$map = &new ComponentMap_Nav( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
