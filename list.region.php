<?php 
 /*********************
06-03-2003  v3.01
Module: Article
Description:   sectional index page  for regions
Calls: list.layout.inc.php
Called By: article.php when  $region is set
To Do:  write shorted sql statement for $list
				make repeat number globally set
*********************/ 

$limit= 100;
$title=$dbcon->CacheExecute("SELECT *  FROM region  WHERE id = $MM_region") or DIE($dbcon->ErrorMsg());  
  $list_name = $title->Fields("title") ;
  $list=$dbcon->CacheExecute("SELECT *  FROM articles   WHERE state=$MM_region and  publish=1 Order by date desc, id desc LIMIT $limit")or DIE($dbcon->ErrorMsg());
  if ($list->RecordCount() == 1) {
  $idvar=$list->Fields("id");
  header ("Location: article.php?id=$idvar");}
	  $page_numRows=0;
    $page__totalRows= $list->RecordCount();
 	if (empty($HTTP_GET_VARS["all"])){$Repeat2__numRows = $limit;}
  		else {$Repeat2__numRows = -1;}
  	 	$Repeat2__index= 0;
 ?>
 <p class="title"><?php  $list_name ?></p>
<?php include ("list.layout.inc.php");


 if ($list->RecordCount() > $limit){?>
                <span class="go"><a href="article.php?region=<?php echo $MM_region; ?>&all=1">More <b>&#187;</b></a></span> <?php }?>
				
