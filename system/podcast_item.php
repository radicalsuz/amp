<?php

require_once("Modules/Podcast/Item/ComponentMap.inc.php");

$map = &new ComponentMap_PodcastItem();
$controller = &$map->get_controller( );
print $controller->execute( );

?>
