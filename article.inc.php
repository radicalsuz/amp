<?php
/*********************
 06-18-2003  v3.01
Module:  Content
Description:  display page  for  articles
CSS:    text,  photocaption, subtitle, title,  author,  date, 
To Do:

*********************/ 
//get data and check to see if we display the page or redirect
 if ($preview == 1) {
   $Recordset1=$dbcon->CacheExecute("SELECT * FROM articles WHERE id = $MM_id") or DIE($dbcon->ErrorMsg());} 
 else{
   $Recordset1=$dbcon->CacheExecute("SELECT * FROM articles WHERE id = $MM_id and publish=1") or header("Location: search.php");//DIE($dbcon->ErrorMsg());
}
   if ($Recordset1->RecordCount() == 0) {header ("Location: index.php");}

  if ($Recordset1->Fields("linkover") == 1){
  $goodbye = $Recordset1->Fields("link");
   header ("Location: $goodbye") ;}
?>
<table width="100%" class="text"><tr><td>
<?php ########## HEADER ########## ?>
<p class="title"><?php echo converttext($Recordset1->Fields("title"))?></p>
<?php if ($Recordset1->Fields("subtitile") != (NULL)) { ?><span class="subtitle"><?php echo converttext($Recordset1->Fields("subtitile"))?></span><br><?php }  //end if for subtitle ?>
<?php if (trim($Recordset1->Fields("author")) != (NULL)) { ?><span class="author">by&nbsp;<?php echo converttext($Recordset1->Fields("author"))?></span><?php } //end if for author ?><?php if (trim(($Recordset1->Fields("author")) != (NULL)) &&  ($Recordset1->Fields("source") != (NULL))) { echo ?>,&nbsp;<?php } ?><?php if ($Recordset1->Fields("source") != (NULL)) { ?><span class="author"><?php if ($Recordset1->Fields("sourceurl") != NULL){echo "<a href=\"".$Recordset1->Fields("sourceurl")."\">";}
echo $Recordset1->Fields("source");
if ($Recordset1->Fields("sourceurl") != NULL){echo "</a>";}
?></span><?php } //end if for author ?><?php if (($Recordset1->Fields("author") != (NULL))or  ($Recordset1->Fields("source") != (NULL))) { echo ?><br><?php } ?>

<?php if ($Recordset1->Fields("contact") != (NULL)) { ?> <span class="author">Contact:&nbsp;<?php echo converttext($Recordset1->Fields("contact")) ?></span><br><?php } ?>
<?php if ($Recordset1->Fields("usedate") != (1))  { 
if ($Recordset1->Fields("date") != "0000-00-00") {
?> <span class="date"><?php echo DoDate( $Recordset1->Fields("date"), 'F jS, Y') ?></span><br><?php } }?>

</td></tr>
<td></td>
<tr><td  class="text">
<?php 
#################IMAGE IMAGE ###############
if ($Recordset1->Fields("picuse") == (1)) { //start of picture 

$fpathtoimg = $base_path_amp.$NAV_IMG_PATH .$Recordset1->Fields("pselection")."/".$Recordset1->Fields("picture");
$pathtoimg = $Web_url.$NAV_IMG_PATH .$Recordset1->Fields("pselection")."/".$Recordset1->Fields("picture");
$imageInfo = getimagesize($fpathtoimg); 
$pwidth = $imageInfo[0]; 

?>
<table width="<?php echo $pwidth ?>" border="0" align="<?php if ($Recordset1->Fields("alignment") == ("left")) {echo "left";} else {echo "right";}?>" cellpadding="0" cellspacing="0"><tr><td><img src="<?php echo $pathtoimg; ?>" alt="<?php echo $Recordset1->Fields("alttag")?>" hspace="4" vspace="4" border="0"></td></tr><Tr align="center"><td width="<?php echo $pwidth ?>" class="photocaption"><?php echo $Recordset1->Fields("piccap")?></td></TR></table>
<p class="text"> 
  <?php } //end of picture ?>
  
<?php  
### BODY TEXT ###
 if ($Recordset1->Fields("html") == (0)) {   // start non html text
     echo hotword(converttext($Recordset1->Fields("test"))); }  //end of non html text
   if ($Recordset1->Fields("html") == (1)) {  //start of html text 
    echo hotword($Recordset1->Fields("test")); } //end of html text
### ACTION ITEM  ###
if ($Recordset1->Fields("actionitem") == (1)){  
	$item = $Recordset1->Fields("actionlink");
	include ("sendfax.inc.php"); }
	
if ($Recordset1->Fields("comments") == (1)){
include ("comments.inc.php"); }

if ($Recordset1->Fields("doc") != NULL){  include ("docbox.inc.php"); }

 $Recordset1->Close();
  ?>
 </td></tr></table>
