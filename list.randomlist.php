<?php 
 /*********************
09-12-2003  v3.01
Module: Article
Description:   sectional index page that shows thre random articles in that section
Functions: DoDate
CSS:listtitle, text
Called By: list.inc.php (as var from database)
To Do:  

*********************/ 
   $limit =3;
   
$sqlsel = "SELECT id, link, linkover, shortdesc, date, usedate, author, source,  sourceurl, picuse, picture, title FROM articles ";
$sql ="  WHERE   $classselect and $MX_type=$MM_type  and publish=1 order by rand() limit 3 ";


$sqlct  = "SELECT  COUNT(DISTINCT id)  from articles".$sql;
$sqlx = $sqlsel.$sql;

$listct=$dbcon->CacheExecute("$sqlct")or DIE($dbcon->ErrorMsg());
 $list=$dbcon->CacheExecute("$sqlx")or DIE($dbcon->ErrorMsg());
	
 ?>
 
<?php include ("list.layout.inc.php");?>
<?php if($limit < $listct->fields[0]) {?>
<div align="right"><span class="go"><a href="article.php?list=type&type=<?php echo $MM_type; ?>&nointro=1">Read 
  More <b>&#187;</b></a></span> </div>
<?php }
				
				?>