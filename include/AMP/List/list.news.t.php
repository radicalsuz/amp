<?php 
/*********************
06-03-2003  v3.01
Module: Article
Description:  news index page  that displays all news in a section
CSS:  go
calls: list.layot.php
Called By: list.navastitle.php
GET VARS: 
   					$all = 1 - overrise the pagation var and show all articles
To Do:
*********************/ 
$classpr=3;
$repeatnum= 50;
$title=$dbcon->CacheExecute("SELECT *  FROM class  WHERE id = $classpr") or DIE($dbcon->ErrorMsg());  
$list_name = $title->Fields("class") ;
$list=$dbcon->CacheExecute("SELECT *  FROM articles  WHERE class=$classpr and $MX_type=$MM_type and publish=1 Order by date desc, id desc LIMIT $repeatnum")or DIE($dbcon->ErrorMsg());
$page_numRows=0;
$page__totalRows= $list->RecordCount();
if (empty($_GET["all"])){
	$Repeat2__numRows = $limit;
}
else {
	$Repeat2__numRows = -1;
}
$Repeat2__index= 0;
echo '<p class="subtitle">'  .  $title->Fields("class")  .  '</p>';
if ($listlayoutreplace !=NULL) {
	include("$listlayoutreplace"); 
}
else{
	include ("AMP/List/list.layout.inc.php"); 
}

if($limit < $listct->fields[0]) {
	echo '<span class="go"><a href="article.php?list=class&class='. $classpr .'">More <b>&#187;</b></a></span>';
}
?>