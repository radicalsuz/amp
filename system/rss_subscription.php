<?php

require_once( 'AMP/Content/RSS/Subscription/ComponentMap.inc.php');
$modid = AMP_MODULE_ID_RSS_SUBSCRIPTIONS;

$map = &new ComponentMap_RSS_Subscription( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
