<?
require_once("Modules/Podcast/ComponentMap.inc.php");
$modid = 46;
$map = &new ComponentMap_Podcast();
$controller = &$map->get_controller( );
print $controller->execute( );

?>
