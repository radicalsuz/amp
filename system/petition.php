<?php

// something needs to deal with the petition redirect
$modid = "7";
$mod_name = 'petition';
require_once( 'Modules/Petition/ComponentMap.inc.php');

$map = &new ComponentMap_Petition( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
