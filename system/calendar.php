<?php

require_once( 'Modules/calendar/ComponentMap.inc.php');

$map = &new ComponentMap_calendar( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
