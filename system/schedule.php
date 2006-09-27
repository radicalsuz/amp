<?php

require_once ('Modules/Schedule/ComponentMap.inc.php');

$map = &new ComponentMap_Schedule();
$controller = &$map->get_controller( );
print $controller->execute( );

?>
