<?php
/*********************
 06-11-2003  v3.01
Module:  Content
Description:  display page  for news articles
CSS:  subtitile,  newstitle,  text,  photocaption
To Do:

*********************/ 

 if ($preview==1) {
   $Recordset1=$dbcon->CacheExecute("SELECT * FROM articles WHERE id = $MM_id") or DIE($dbcon->ErrorMsg());} 
 else{
   $Recordset1=$dbcon->CacheExecute("SELECT * FROM articles WHERE id = $MM_id and publish=1") or DIE($dbcon->ErrorMsg());}
    if ($Recordset1->RecordCount() == 0) {header ("Location: index.php");}
	
if ($Recordset1->Fields("linkover") == 1){
  $goodbye = $Recordset1->Fields("link");
   header ("Location: $goodbye") ;}

?>
<table width="100%" class="text"><tr><td>
<?php ########## HEADER ########## ?>
<span class="newstitle"><?php echo converttext($Recordset1->Fields("title"))?></span><br>

<?php if ($Recordset1->Fields("subtitile") != (NULL)) { ?><br><span class="newssubtitle"><?php echo converttext($Recordset1->Fields("subtitile"))?></span><br><?php }  //end if for subtitle ?>
<br>
<?php if (trim($Recordset1->Fields("author")) != (NULL)) { ?><span class="newssubtitle">by&nbsp;<?php echo converttext($Recordset1->Fields("author"))?></span><?php } //end if for author ?><?php if ((trim($Recordset1->Fields("author")) != (NULL)) &&  ($Recordset1->Fields("source") != (NULL))) { echo ?>,&nbsp;<?php } ?><?php if ($Recordset1->Fields("source") != (NULL)) { ?><span class="newssubtitle"><?php if ($Recordset1->Fields("sourceurl") != NULL){echo "<a href=\"".$Recordset1->Fields("sourceurl")."\">";}
echo $Recordset1->Fields("source");
if ($Recordset1->Fields("sourceurl") != NULL){echo "</a>";}
?></span><?php } //end if for author ?><?php if (($Recordset1->Fields("author") != (NULL))or  ($Recordset1->Fields("source") != (NULL))) { echo ?><br><?php } ?>
<?php if ($Recordset1->Fields("contact") != (NULL)) { ?> <span class="newssubtitle">Contact: <?php echo converttext($Recordset1->Fields("contact")) ?></span><br><?php } ?>
<span class="newssubtitle"><?php 
 if ($Recordset1->Fields("usedate") != (1)) { 
 echo DoDate( $Recordset1->Fields("date"), 'F jS, Y') ;}?></span><br>

</td></tr>
<td><img src="<?php echo $Web_url.$NAV_IMG_PATH ?>s.gif" width=8 height=5></td>
<tr><td class="text">
<?php 
#################IMAGE IMAGE ###############
if ($Recordset1->Fields("picuse") == (1)) { //start of picture 

$fpathtoimg = $base_path_amp.$NAV_IMG_PATH .$Recordset1->Fields("pselection")."/".$Recordset1->Fields("picture");
$pathtoimg = $Web_url.$NAV_IMG_PATH .$Recordset1->Fields("pselection")."/".$Recordset1->Fields("picture");
$imageInfo = getimagesize($fpathtoimg); 
$pwidth = $imageInfo[0]; 

?>
<table align="<?php if ($Recordset1->Fields("alignment") == ("left")) {echo "left";} else {echo "right";}?>" width="<?php echo $pwidth ?>"><tr><td><img src="<?php echo $pathtoimg; ?>" alt="<?php echo $Recordset1->Fields("alttag")?>" hspace="4" vspace="4" border="1"></td></tr><Tr align="center"><td width="<?php echo $pwidth ?>" class="photocaption"><?php echo $Recordset1->Fields("piccap")?></td></TR></table>
<p class="text"> 
  <?php } //end of picture ?>
  
<?php  
### BODY TEXT ###
 if ($Recordset1->Fields("html") == (0)) {   // start non html text
     echo converttext($Recordset1->Fields("test")); }  //end of non html text
   if ($Recordset1->Fields("html") == (1)) {  //start of html text 
    echo $Recordset1->Fields("test"); } //end of html text
### ACTION ITEM  ###
if ($Recordset1->Fields("actionitem") == (1)){  //start of action item
include ("sendfax.inc.php"); } //end of action item

if ($Recordset1->Fields("doc") != NULL){  include ("docbox.inc.php"); }

 $Recordset1->Close();
 ?>
 </td></tr></table>