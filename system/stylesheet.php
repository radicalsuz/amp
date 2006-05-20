<?php

require_once( 'AMP/Content/Stylesheet/ComponentMap.inc.php');

$map = &new ComponentMap_Stylesheet( );

$controller = & $map->get_controller( );
print $controller->execute( );

?>
