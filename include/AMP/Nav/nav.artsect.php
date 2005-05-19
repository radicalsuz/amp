<?php
//get subsections

// get content

//show current section name

$artsql = "select id, title from articles where publish=1 and uselink =1 and type = $MM_type and class != 8  and class !=2 order by pageorder, date ";
$secsql = "select id, type from articletype where usenav=1 and parent =$MM_type order by textorder asc";
   	$art=$dbcon->Execute($artsql) or DIE($dbcon->ErrorMsg());
   	$sec=$dbcon->Execute($secsql) or DIE($dbcon->ErrorMsg());


if ($sec->RecordCount() > 0) {
$x =1;
while (!$sec->EOF) {
    if (!isset($shownav)) $shownav = "";
	if ($x > 1) {$shownav.=$lNAV_HTML_3 ;} 
	$shownav.='<a href="section.php?id='.$sec->Fields("id").'" class="sidelist">'.$sec->Fields("type").'</a>';
	$shownav.=$lNAV_HTML_4 ;
	$sec->MoveNext();
	$x++;
}
if ($art->RecordCount() > 0) { $shownav.=$lNAV_HTML_3 ;}
}

if ($art->RecordCount() > 0) {
$y =1;
while (!$art->EOF) {
    if (!isset($shownav)) $shownav = "";
	if ($y > 1) {$shownav.=$lNAV_HTML_3 ;} 
	$shownav.='<a href="article.php?id='.$art->Fields("id").'" class="sidelist">'.$art->Fields("title").'</a>';
	$shownav.=$lNAV_HTML_4 ; //start link text
	$art->MoveNext();
	$y++;
}}
if (isset($shownav)) echo $shownav;

?>
