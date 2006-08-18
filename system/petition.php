<?php

// something needs to deal with the petition redirect
require_once( 'Modules/Petition/ComponentMap.inc.php');
$modid = AMP_MODULE_ID_PETITION;

$map = &new ComponentMap_Petition( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
