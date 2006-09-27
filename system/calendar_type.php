<?php

require_once( 'Modules/Calendar/Type/ComponentMap.inc.php');

$map = &new ComponentMap_Calendar_Type( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
