<?php 
if ( AMP_USE_OLD_CONTENT_ENGINE ) {
    $sidegraphic=$dbcon->Execute("select parent, flash from articletype where id = $MM_type") or DIE($dbcon->ErrorMsg());

    while ($sideimg == NULL){
    $sideimg = $sidegraphic->Fields("flash");
    $sparent = $sidegraphic->Fields("parent");
    if ($sideimg == NULL) {
    $sidegraphic=$dbcon->Execute("select parent, flash from articletype where id = $sparent") or DIE($dbcon->ErrorMsg());}
    } 
} else {
    $currentPage = &AMPContent_Page::instance();
    $map = &AMPContent_Map::instance();
    $sideimg =$map->readAncestors( $currentPage->section_id, 'flash' );
}
echo $sideimg;
?>
