<?php
##to do :
#  duplicat checking against email
#  email verifaction
# email dns lookup
# required fields
#  response pages 

# modinput2 needs to be changed to modinput 4 and not break

$modid = 7;
$mod_id = 42;
include_once("AMP/BaseDB.php");
include_once("AMP/BaseTemplate.php");
include_once("AMP/BaseModuleIntro.php");  



$petitontx=$dbcon->Execute("SELECT * FROM petition where id = ".$_GET["pid"]) or DIE("could not find petition".$dbcon->ErrorMsg());
$petmod  =  $petitontx->Fields("udmid");
$_GET["modin"]=$petmod;
$ptct= $dbcon->CacheExecute("SELECT  COUNT(DISTINCT id) FROM userdata  where modin = $petmod and custom19 = 1 order by id desc ") or DIE("could not get count".$dbcon->ErrorMsg());
$count = $ptct->fields[0];
if ($petitontx->Fields("datestarted") !="0000-00-00" or $petitontx->Fields("datestarted") != NULL ){
$petition_started = DoDate($petitontx->Fields("datestarted"),"M, j Y");}
if ($petitontx->Fields("dateended") !="0000-00-00" or $petitontx->Fields("dateended") != NULL){
$petition_ends= DoDate($petitontx->Fields("dateended"),"M, j Y");}

function progressBox() {
	global   $petition_started,  $petition_ends, $count ;
	echo "<table cellpadding=0 cellspacing=0 border=1 align=center bgcolor=\"#CCCCCC\" width=\"100%\"><tr><td>";
	echo "\n\t<table border=0 cellspacing=0 cellpadding=0 width=\"100%\"><tr>";
	if  ($petition_started){
		echo "\n\t\t<td align=center class=form><small><B>Posted:<br>$petition_started</B></small></td>";
	}
	if  ($petition_ends){
		echo "\n\t\t<td align=center class=form><B><small>Petition Ends:<br>$petition_ends</small></B></td>";
	}
	echo "\n\t\t<td align=center class=form><small><B>Petition Signatures:&nbsp; $count</b></small></td>";
	echo "\n\t</tr></table>";
	echo "</td></tr></table>";
}

if ($_GET["pthank"] ) { 
	echo "<p>Thank you for signing this petition.</p><p></p><p></p>";
}

if ($_GET["signers"] or $_GET["pthank"]  ) {
	if (!$_GET["offset"]) {$offset= 0;}
  	else {$offset=$_GET["offset"];}
	$pdif=$dbcon->CacheExecute("SELECT First_Name, Last_Name, Company,Notes, City,  State  FROM userdata  where  modin = $petmod and u.custom19 = 1 order by .id desc  Limit $offset, 25") or DIE("could not find signers".$dbcon->ErrorMsg());
?>
<a name="namelist"></a>
<p class="title">Recent Petition Signers</p>
  
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr bgcolor="#CCCCCC"> 
    <td class="text">Name</td>
    <td class="text">Organization</td>
    <td class="text">Location</td>
    <td class="text">Comment</td>
  </tr>
  <?php while (!$pdif->EOF) { ?>
  <tr> 
    <td class="text"><?php echo trim($pdif->Fields("First_Name"));?> <?php echo trim($pdif->Fields("Last_Name"));?></td>
    <td class="text"><?php echo $pdif->Fields("Company");?></td>
    <td class="text"><?php echo $pdif->Fields("City");?> <?php echo $pdif->Fields("State");?></td>
    <td class="text"><?php echo $pdif->Fields("Notes");?></td>
  </tr>
  <?php  $pdif->MoveNext();}
  if ($count > 25) {
  ?>
  <tr> 
    <td class="text"></td>
    <td class="text"></td>
    <td class="text"></td>
    <td class="text"><a href="petition.php?pid=<?php echo $_GET["pid"];?>&signers=1&offset=<?php echo ($offset + 25) ; ?>#namelist">Next 
      Page</a></td>
  </tr><?php } ?>

  
</table>
<P><a href="petition.php?pid=<?php echo $_GET["pid"];?>">Sign the Petition</a></P>
<?php
	echo "<br><br>";
}

if(!$inputSubmit){
	progressBox();
	echo "<P align=center><a href=\"petition.php?pid=".$_GET["pid"]."&signers=1\">View Signatures</a></p>";
?>
      <p class="title"> 
        <?php echo $petitontx->Fields("title")?>
      </p>
 
<?php if ($petitontx->Fields("addressedto") != NULL) {?><p><B><span class="bodystrong">To:</span> <span class="text"> 
  <?php echo $petitontx->Fields("addressedto")?>
  </span></B></p><?php } ?>
<p class="text"> 
  <?php echo converttext( $petitontx->Fields("text")) ?>
</p><?php if ($petitontx->Fields("intsigner") != NULL) {?>
<p><B><span class="bodystrong">Initiated By:</span>  
  <?php echo $petitontx->Fields("intsigner")?>
  , 
  <?php echo $petitontx->Fields("org")?>
  <a href="http://<?php echo $petitontx->Fields("url");?>"> 
  <?php echo $petitontx->Fields("url");?>
  </a><br>
  <?php echo $petitontx->Fields("intsignerad")?>
  <a href="mailto:<?php echo $petitontx->Fields("intsignerem")?>"> 
  <?php echo $petitontx->Fields("intsignerem")?>
  </a></span></B></p><?php } ?><br>
<?php


 //recentSignatures(); 

	if (!$_GET[pthank] ) {
		$modin =$petmod  ;
?><p class="title">Sign Petition</p><?php
		include ("modinput4.php" );
	}
}

include_once("AMP/BaseFooter.php");
?>
