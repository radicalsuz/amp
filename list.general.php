<?php
 $classselect = "(class !=2 && class !=8 && class !=9)";
 $sql="  WHERE $MX_type=$MM_type and publish=1 and   $classselect   Order by  pageorder asc, date desc, id desc";
   $section=$dbcon->CacheExecute("SELECT *  FROM articletype WHERE id=$MM_type")or DIE($dbcon->ErrorMsg()); 
   if  ($AMP_view_rel){
   $sql="  WHERE ($MX_type=$MM_type or relsection1 = $MM_type or relsection2 = $MM_type) and publish=1 and   $classselect   $sqlorder";}
     if  ($MM_reltype){
$sql="  Left Join articlereltype  on articleid = id  WHERE (type=$MM_type or typeid = $MM_type) and publish=1 and   $classselect   $sqlorder";
}
$sqlct  = "SELECT  COUNT(DISTINCT id)  from articles".$sql;
$listct=$dbcon->CacheExecute("$sqlct")or DIE($dbcon->ErrorMsg());
$sqlsel = "SELECT  DISTINCTROW  id, link, linkover, shortdesc, date, usedate, author, source, source, sourceurl, picuse, picture, title FROM articles";

if (isset($_GET[offset])) {$soffset = $_GET[offset];} 
else {$soffset = 0;}
$sqloffset = " LIMIT $soffset,$limit ";
if (isset($_GET[all])) {$sqloffset ="";}

$sql = $sqlsel.$sql.$sqloffset;
$list=$dbcon->CacheExecute("$sql")or DIE($dbcon->ErrorMsg());


   if ($listlayoutreplace !=NULL) {include("$listlayoutreplace"); 
		}	else{include ("list.layout.inc.php"); };?>


<br><br>
<div align="right"> 



  <?php
  if ($limit < $listct->fields[0]) {
  
  $MM_removeList = "&offset=, &all=,&nointro=";
reset ($HTTP_GET_VARS);
while (list ($key, $val) = each ($HTTP_GET_VARS)) {
	$nextItem = "&".strtolower($key)."=";
	if (!stristr($MM_removeList, $nextItem)) {
		$MM_keepURL .= "&".$key."=".urlencode($val);
	}
}
$MM_moveFirst=   $PHP_SELF."?".$MM_keepURL."&offset=0";
$MM_moveNext =  $PHP_SELF."?".$MM_keepURL."&nointro=1&offset=".($soffset+$limit);
$MM_movePrev =  $PHP_SELF."?".$MM_keepURL."&nointro=1&offset=".($soffset-$limit);
$loffset = (floor($listct->fields[0] / $limit) * $limit);
$MM_moveLast =  $PHP_SELF."?".$MM_keepURL."&nointro=1&offset=".($loffset);


  ?>
  <?php if ( $soffset  != 0 && $all !=1 ) { ?>
  &nbsp; <a href="<?php echo $MM_moveFirst?>" class="go"> 
  &laquo;&nbsp;First Page</a> 

  <?php  } if ( $soffset  != 0 && $all !=1 ) { ?>
  &nbsp; <a href="<?php echo $MM_moveFirst?>" class="go">&laquo;&nbsp;</a><a href="<?php echo $MM_movePrev?>" class="go">Previous 
  Page </a> 

 <?php  } if ( $soffset  != $loffset  && $all !=1) { ?>
  &nbsp;&nbsp; <a href="<?php echo $MM_moveNext?>" class="go">Next 
  Page &raquo;</a> 

 <?php  } if ( $soffset  != $loffset && $all !=1 ) { ?>
  &nbsp;&nbsp; <a href="<?php echo $MM_moveLast?>" class="go">Last 
  Page &raquo;</a> 

  <?php 
  } if($all !=1 ) { ?>

  &nbsp;&nbsp; <span class="go"><a href="<?php echo $PHP_SELF."?".$MM_keepURL;?>&all=1&nointro=1">All 
  Articles&raquo;</a></span> 
  <?php 
  }
  }
  ?>
</div> 