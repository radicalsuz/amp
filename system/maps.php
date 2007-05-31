<?
require_once("AMP/Geo/FlashMap/ComponentMap.inc.php");

$map = &new ComponentMap_FlashMap();

$controller = &$map->get_controller( );

print $controller->execute( );

?>

