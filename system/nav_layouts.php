<?php

require_once( 'AMP/Content/Nav/Layout/ComponentMap.inc.php');
$map = &new ComponentMap_Nav_Layout( );

$controller = &$map->get_controller( );
print $controller->execute( );

?>
