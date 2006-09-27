<?php
require_once("AMP/System/IntroText/ComponentMap.inc.php");

$map = &new ComponentMap_IntroText();
$controller = &$map->get_controller( );

print $controller->execute( );

?>

