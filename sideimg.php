<?php 
$sidegraphic=$dbcon->Execute("select parent, flash from articletype where id = $MM_type") or DIE($dbcon->ErrorMsg());

while ($sideimg == NULL){
$sideimg = $sidegraphic->Fields("flash");
$sparent = $sidegraphic->Fields("parent");
if ($sideimg == NULL) {
$sidegraphic=$dbcon->Execute("select parent, flash from articletype where id = $sparent") or DIE($dbcon->ErrorMsg());}
} 
echo $sideimg;
?>