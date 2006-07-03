<?php

require_once( 'AMP/Content/Article/Version/ComponentMap.inc.php' );

$map  = & new ComponentMap_Article_Version( );
$controller = & $map->get_controller( );

print $controller->execute( );

?>
