<?php

require_once( 'AMP/Content/Tag/ComponentMap.inc.php');

$map = &new ComponentMap_AMP_Content_Tag( );
$controller = &$map->get_controller( );

print $controller->execute( );

?>
