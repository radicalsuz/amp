<?php

require_once( 'AMP/Content/Article/Frontpage/ComponentMap.inc.php' );

$map = &new ComponentMap_Article_Frontpage( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
