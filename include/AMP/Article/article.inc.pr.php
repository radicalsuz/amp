<?php
/*********************
 06-11-2003  v3.01
Module:  Content
Description:  display page  for  press releases 
CSS:    text,  photocaption, bodygreystrong,  bodygrey, newstitle, newssubtitle
To Do:

*********************/ 

if (isset($preview) && $preview==1) {
	$Recordset1=$dbcon->CacheExecute("SELECT * FROM articles WHERE id = $MM_id") or DIE($dbcon->ErrorMsg());
} else {
	$Recordset1=$dbcon->CacheExecute("SELECT * FROM articles WHERE id = $MM_id and publish=1") or DIE($dbcon->ErrorMsg());
}

if ($Recordset1->RecordCount() == 0) {
	header ("Location: index.php");
}
if ($Recordset1->Fields("linkover") == 1){
  $goodbye = $Recordset1->Fields("link");
   header ("Location: $goodbye") ;}

$Recordset1_numRows=0;
$Recordset1__totalRows=$Recordset1->RecordCount();

?>

<table width="100%" class="text"><tr><td>
<?php ########## HEADER ########## ?>

<?php if ($Recordset1->Fields("date") != (NULL)) { ?><span class="bodygreystrong">For Immediate Release:</span>&nbsp;<span class="bodygrey"><?php echo DoDate( $Recordset1->Fields("date"), 'F jS, Y') ?></span><br><?php } ?>
<?php if ($Recordset1->Fields("contact") != (NULL)) { ?>
      <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top"><span class="bodygreystrong">Contact:&nbsp;</span></td>
          <td valign="top">&nbsp;</td>
          <td valign="top"><span class="bodygrey"><?php echo converttext($Recordset1->Fields("contact")) ?></span><br></td>
        </tr>
      </table>
<?php } ?>

</td></tr>
<td><img src="<?php echo $Web_url?>img/spacer.gif" width=8 height=5></td>
<tr><td class="text">
<span class="newstitle"><?php echo converttext($Recordset1->Fields("title"))?></span><br><br>

<?php if ($Recordset1->Fields("subtitile") != (NULL)) { ?><span class="newssubtitle"><?php echo converttext($Recordset1->Fields("subtitile"))?></span><br><br><?php }  //end if for subtitle ?>

<?php 

#################IMAGE IMAGE ###############

if ($Recordset1->Fields("picuse") == (1)) { //start of picture 

	$fpathtoimg = AMP_LOCAL_PATH.DIRECTORY_SEPARATOR.$NAV_IMG_PATH .$Recordset1->Fields("pselection")."/".$Recordset1->Fields("picture");
	$pathtoimg = $Web_url.$NAV_IMG_PATH .$Recordset1->Fields("pselection")."/".$Recordset1->Fields("picture");
	$imageInfo = getimagesize($fpathtoimg); 
	$pwidth = $imageInfo[0]; 

?>

<table align="<?php if ($Recordset1->Fields("alignment") == ("left")) {echo "left";} else {echo "right";}?>" width="<?php echo $pwidth ?>"><tr><td><img src="<?php echo $pathtoimg; ?>" alt="<?php echo $Recordset1->Fields("alttag")?>" hspace="4" vspace="4" border="1" class="img_main"></td></tr><Tr align="center"><td width="<?php echo $pwidth ?>" class="photocaption"><?php echo $Recordset1->Fields("piccap")?></td></TR></table>
<p class="text"> 

<?php

	//end of picture
}

### BODY TEXT ###
if ($Recordset1->Fields("html") == (0)) {
	// non html text
	echo converttext($Recordset1->Fields("test"));
}

if ($Recordset1->Fields("html") == (1)) {
	// html text 
	echo $Recordset1->Fields("test");
}

### ACTION ITEM  ###
if ($Recordset1->Fields("actionitem") == (1)) {
	// action item
	include ("sendfax.inc.php");
}

if ($Recordset1->Fields("doc") != NULL) {
	include ("docbox.inc.php");
}

$Recordset1->Close();
?>

</td></tr></table>
