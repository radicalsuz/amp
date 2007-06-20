<?php

require_once( 'AMP/User/Profile/ComponentMap.inc.php');

$map = &new ComponentMap_User_Profile( );
$controller = &$map->get_controller( );
print $controller->execute( );


?>
