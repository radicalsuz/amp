<?php 

$currentPage = &AMPContent_Page::instance();
$map = &AMPContent_Map::instance();
echo $map->readAncestors( $currentPage->getSectionId(), 'flash' );

?>
