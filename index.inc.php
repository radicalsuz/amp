<?php
/*********************
09-12-2003  v3.01
Module:  Index
Description:  display index page content
CSS: text, photocaption, hometitle,  subtitle, homebody
SYS VARS: $NAV_IMG_PATH
functions  getimagesize
To Do: 
*********************/ 
   $maintext=$dbcon->CacheExecute("SELECT *  FROM articles  WHERE class=2 and publish='1'  ORDER BY pageorder asc") or DIE($dbcon->ErrorMsg());

?>
<table width="100%" class="text"><?php while  (!$maintext->EOF)
   { ?><tr><td>


<?php #################IMAGE IMAGE ###############
if ($maintext->Fields("picuse") == (1)) { //start of picture 

$fpathtoimg = $base_path_amp.$NAV_IMG_PATH .$maintext->Fields("pselection")."/".$maintext->Fields("picture");
$pathtoimg = $NAV_IMG_PATH .$maintext->Fields("pselection")."/".$maintext->Fields("picture");
$imageInfo = getimagesize($fpathtoimg); 
$pwidth = $imageInfo[0]; 
$pheight = $imageInfo[1];
?>
<table width="<?php echo $pwidth ?>" border="0" align="<?php if ($maintext->Fields("alignment") == ("left")) {echo "left";} else {echo "right";}?>" cellpadding="0" cellspacing="0"><tr><td><img src="<?php echo $pathtoimg; ?>" alt="<?php echo $maintext->Fields("alttag")?>" vspace="4" hspace="4" border="1" width="<?php echo $pwidth ?>" height="<?php echo $pheight ?>"></td></tr><tr align="center"><td width="<?php echo $pwidth ?>" class="photocaption"><?php echo $maintext->Fields("piccap")?></td></TR></table>
<?php } //end of picture ?>

<?php if ($maintext->Fields("title") != (NULL)) {?><p class="hometitle"><?php if ($maintext->Fields("usemore") == ('1')) { ?><a href="<?php echo $maintext->Fields("morelink")?>" class="hometitle"><?php } echo $maintext->Fields("title");?><?php if ($maintext->Fields("usemore") == ('1')) { echo "</a>"; }?></p><?php }?>
<?php if ($maintext->Fields("subtitile") != (NULL)) { ?><span class="subtitle"><?php echo $maintext->Fields("subtitile")?></span><?php }  //end if for subtitle ?>

<span class="homebody"> <p class="homebody"><?php  
### BODY TEXT ###
 if ($maintext->Fields("html") == (0)) {   // start non html text
     echo converttext($maintext->Fields("test")); }  //end of non html text
   if ($maintext->Fields("html") == (1)) {  //start of html text 
    echo $maintext->Fields("test"); } //end of html text ?>
<?php if ($maintext->Fields("usemore") == ('1')) { ?>
</span> <br><br><div align="right"><a href="<?php echo $maintext->Fields("morelink")?>">Read More&nbsp;&#187;</a>&nbsp;&nbsp; </div>
<?php } ?>
</p></span><br>
  

</td></tr>
<?php  $maintext->MoveNext();  }?>
</table>