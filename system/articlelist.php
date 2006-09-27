<?php

require_once( 'AMP/Content/Article/ComponentMap.inc.php' );

$map = &new ComponentMap_Article( );
$controller = &$map->get_controller( );
$controller->request( 'view' );
print $controller->execute( );


?>
