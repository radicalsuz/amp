<?php

require_once( 'AMP/Content/Section/ComponentMap.inc.php');

$map = &new ComponentMap_Section( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
