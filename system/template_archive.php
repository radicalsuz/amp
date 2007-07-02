<?php

require_once( 'AMP/Content/Template/Archive/ComponentMap.inc.php');

$map = &new ComponentMap_Template_Archive( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
