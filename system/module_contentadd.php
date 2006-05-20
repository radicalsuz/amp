<?php
$mod_name="module";
require_once( 'AMP/Content/PublicPage/ComponentMap.inc.php');
$map = &new ComponentMap_PublicPage( );
$controller = &$map->get_controller( );
print $controller->execute( );
?>
