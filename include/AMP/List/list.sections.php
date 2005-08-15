<?php 
 /*********************
09-12-2003  v3.01
Module: Article
Description:   sectional index page  for sections in a section
Functions: DoDate
CSS:listtitle, text
Called By: list.inc.php (as var from database)
To Do:  

*********************/ 
$maxTextLenght=9000;
//populate list 

$list=$dbcon->CacheExecute("SELECT uselink, id, linkurl, image2, type, description, date2 FROM articletype  WHERE parent=$MM_type and usenav=1 order by date2 desc, textorder asc")or DIE($dbcon->ErrorMsg());  
  
while (!$list->EOF) { ?><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
	<?php if ($list->Fields("image2") != (NULL)) { ?><img src="<?php echo $list->Fields("image2")?>"align="left" vspace="2" hspace="4" border="0"> <?php } ?>
	</td>
    <td width="100%" valign="top">

 <a href="<?php if ($list->Fields("uselink") != (1)) { ?>article.php?list=type<?php if (isset($MM_rel) && $MM_rel) {echo"r" ;}?>&type=<? echo $list->Fields("id")?><?php }?>
<?php if ($list->Fields("uselink") == (1)) { ?><?php echo $list->Fields("linkurl")?><?php }?>" class="listtitle"  >
  <?php echo $list->Fields("type")  //end title ?></a> 
<span class="text"> 
<?php  
	if ($list->Fields("description") != NULL ){
 		echo "<br>";
		//echo "&nbsp;-&nbsp;";
		$aspace=" ";
		$ttext =$list->Fields("description");
		if(strlen($ttext) > $maxTextLenght ) {
			$ttext = substr(trim($ttext),0,$maxTextLenght); 
			$ttext = substr($ttext,0,strlen($ttext)-strpos(strrev($ttext),$aspace));
			$ttext = $ttext.'...';
  		}
  		echo (converttext($ttext)); 
	}
	echo '&nbsp;'; 
	//date
	if ($list->Fields("date2") != "0000-00-00") { 
		echo  DoDate( $list->Fields("date2"), '(F, Y)') ; 
	}
?>
 </td>
  </tr>
</table>

<br>
<?php
	$list->MoveNext();
}
?>
