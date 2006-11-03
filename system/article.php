<?php

require_once( 'AMP/Content/Article/ComponentMap.inc.php');

$map = &new ComponentMap_Article();
$controller = &$map->get_controller( );
print $controller->execute( );
ob_end_flush( );
?>
