<?php

require_once( '%4\$s%1\$s/ComponentMap.inc.php');

$map = &new ComponentMap_%1\$s( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
