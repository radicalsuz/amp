<?php 
/*********************
06-03-2003  v3.01
Module: Article
Description:  press release  index page  in newsroom
CSS:  go
calls: list.layot.php
Called By: list.newsroom.php
GET VARS: 
   					$all = 1 - overrise the pagation var and show all articles
To Do:
*********************/ 

$classpr=5;
$title=$dbcon->CacheExecute("SELECT class  FROM class  WHERE id = $classpr") or DIE($dbcon->ErrorMsg());  
$list_name = $title->Fields("class") ;

$sqlsel = "SELECT id, link, linkover, shortdesc, date, usedate, author, source,  sourceurl, picuse, picture, title FROM articles ";
$sql = " WHERE class=$classpr and  publish=1 Order by date desc, id desc ";
$soffset = 0;
$sqloffset = " LIMIT $soffset, $limit "; 
$sqlct  = "SELECT  COUNT(DISTINCT id)  from articles".$sql;
$sqlx = $sqlsel.$sql.$sqloffset;

$listct=$dbcon->CacheExecute("$sqlct")or DIE($dbcon->ErrorMsg());
$list=$dbcon->CacheExecute("$sqlx")or DIE($dbcon->ErrorMsg());
	
?>

<p class="subtitle"><?php //echo $title->Fields("class") ?></p>

<?php

if ($listlayoutreplace !=NULL) {include("$listlayoutreplace"); 
		}	else{include ("AMP/List/list.layout.inc.php"); };
if($limit < $listct->fields[0]) {
?>

	<span class="go"><a href="article.php?list=class&class=<?php echo $classpr; ?>">More <b>&#187;</b></a></span>

<?php
}

$list->Close();
$title->Close();

?>
