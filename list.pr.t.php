<?php 
/*********************
06-03-2003  v3.01
Module: Article
Description: press release index page  that displays all press releases in a section
CSS:  go
calls: list.layot.php
Called By: list.navastitle.php
GET VARS: 
   					$all = 1 - overrise the pagation var and show all articles
To Do:
*********************/ 
$classpr=10;
$repeatnum= 50;
$title=$dbcon->CacheExecute("SELECT *  FROM class  WHERE id = $classpr") or DIE($dbcon->ErrorMsg());  
  $list_name = $title->Fields("class") ;
  $list=$dbcon->CacheExecute("SELECT *  FROM articles  WHERE class=$classpr and $MX_type=$MM_type and publish=1 Order by date desc, id desc LIMIT $repeatnum")or DIE($dbcon->ErrorMsg());
 $page_numRows=0;
    $page__totalRows= $list->RecordCount();
 	if (empty($HTTP_GET_VARS["all"])){$Repeat2__numRows = $limit;}
  		else {$Repeat2__numRows = -1;}
  	 	$Repeat2__index= 0;
	
 ?>
 <p class="subtitle"><?php echo $title->Fields("class") ?></p>
<?php include ("list.layout.inc.php");?>
<?php if ($list->RecordCount() > $limit){?>
                <span class="go"><a href="article.php?list=class&class=<?php echo $classpr; ?>">More <b>&#187;</b></a></span> <?php }
				 $list->Close();
				 $title->Close(); ?>