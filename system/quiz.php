<?php

require_once("Modules/Quiz/ComponentMap.inc.php");
require_once("AMP/System/Page.inc.php");
$modid = 46;
$map = &new ComponentMap_Quiz();

$controller = &$map->get_controller( );
print $controller->execute( );


?>
