 <?php
 /*********************
07-24-2003  v3.01
Module: Article
Description:   sectional index page  for content in sections in a section
CSS: subtitle
Calls: list.layout.inc.php
Called By: list.inc.php (as var from database)
To Do:  

*********************/ 
 

 $title=$dbcon->CacheExecute("SELECT type, description, id FROM articletype WHERE parent=$MM_type order by textorder asc")or DIE($dbcon->ErrorMsg());

   
 while (!$title->EOF) 
   { 
//start subtype
$list=$dbcon->CacheExecute("SELECT id, link, linkover, shortdesc, date, usedate, author, source, source, sourceurl, picuse, picture, title FROM articles  Where type=". $title->Fields("id") . " and publish=1 and class !=8 order by date desc") or DIE($dbcon->ErrorMsg());
	$page_numRows=0;
    $page__totalRows= $list->RecordCount();
	if  ($page__totalRows > 0) {

 ?>
<p><span class="title"><?php echo $title->Fields("type")  //end sub name ?></span> <br>
<?php
 if ($title->Fields("description") != NULL) { 
echo converttext($title->Fields("description"))."<br>"; }?>

<br>

<?php


 	if (empty($HTTP_GET_VARS["all"])){$Repeat2__numRows = $limit;}
  		else {$Repeat2__numRows = -1;}
  	 	$Repeat2__index= 0;
		?>

<?php if (isset($listlayoutreplace) && $listlayoutreplace !=NULL) {include("$listlayoutreplace"); 
		}	else{include ("AMP/List/list.layout.inc.php"); }?>
 <?php
 }
  $title->MoveNext();
}

?>
