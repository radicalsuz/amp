<?php   
$modid=14;
$mod_id = 44;
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php"); ?>
<?php
$areacalled__MMColParam = $HTTP_GET_VARS["area"];
if  ((isset($HTTP_GET_VARS["area"])) && (($HTTP_GET_VARS["area"]) != "national")) {
$area=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states inner join moduserdata on states.id=moduserdata.State where moduserdata.publish=1 and moduserdata.modinid=6 and moduserdata.State = $areacalled__MMColParam Order by states.statename asc") or DIE($dbcon->ErrorMsg());
}

else   {
if (isset($HTTP_GET_VARS[traintype]) )
{ $traintypesql = "and moduserdata.field3= 2";}
if ($HTTP_GET_VARS[traintype]== ("3") )
		{$traintypesql = "and moduserdata.field3= 1" ;}
	else if ($HTTP_GET_VARS[traintype]==4)
		{$traintypesql = "and moduserdata.field4= 1" ;}
	else if ($HTTP_GET_VARS[traintype]==5)
		{$traintypesql = "and moduserdata.field5= 1";}
	else if ($HTTP_GET_VARS[traintype]==6)
		{$traintypesql = "and moduserdata.field6= 1";}
  if ($HTTP_GET_VARS[traintype]==7) 
  		{$traintypesql = "and moduserdata.field7= 1";} 
  if ($HTTP_GET_VARS[traintype]==8) 
  		{$traintypesql = "and moduserdata.field8= 1";} 
   $area=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states inner join moduserdata on states.id=moduserdata.State where moduserdata.publish=1 and moduserdata.modinid=6 $traintypesql Order by states.statename asc") or DIE($dbcon->ErrorMsg());
   }
   
   $area_numRows=0;
   $area__totalRows=$area->RecordCount();
   
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $area_numRows = $area_numRows + $Repeat1__numRows;
?>

      <p class="text"> 
	   <a href="modinput.php?modin=6" class="text"> 
        List Yourself as a Speaker</a></p>
		
	<?php
 if  (isset($HTTP_GET_VARS["area"]) && ($HTTP_GET_VARS["area"] != "300")) { //start area called
if ($area__totalRows == 0){ //start failed area called
echo "<h2>".$area->Fields("statename")."</h2>";?>
<p class="text">There are currently no speakers listed in this area.</p>
<?php }//end failed area called
 } //end areacalled
 
 if  (isset($HTTP_GET_VARS["traintype"]) ) { //start area called
if ($area__totalRows == 0){ //start failed area called?>
 
<p class="text">There are currently no speakers of this type listed.</p>
<?php }//end failed area called
 } //end areacalled?>
		
<?php while (($Repeat1__numRows-- != 0) && (!$area->EOF)) 
   { 
?>  
<p class="blueheading"> <b>
  <a name="<?php echo $area->Fields("id")?>"></a><?php echo $area->Fields("statename")?>
  </b> </p>
<?php
   $MM_linkid=$area->Fields("id");

	 if ($HTTP_GET_VARS[traintype]== ("3") )
		{$traintypesql = "and moduserdata.field3= 1" ;}
	else if ($HTTP_GET_VARS[traintype]==4)
		{$traintypesql = "and moduserdata.field4= 1" ;}
	else if ($HTTP_GET_VARS[traintype]==5)
		{$traintypesql = "and moduserdata.field5= 1";}
	else if ($HTTP_GET_VARS[traintype]==6)
		{$traintypesql = "and moduserdata.field6= 1";}
  if ($HTTP_GET_VARS[traintype]==7) 
  		{$traintypesql = "and moduserdata.field7= 1";} 
  if ($HTTP_GET_VARS[traintype]==8) 
  		{$traintypesql = "and moduserdata.field8= 1";} 
					
   $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state FROM moduserdata left join states on states.id=moduserdata.State WHERE moduserdata.State =$MM_linkid and moduserdata.publish = '1' and moduserdata.modinid=6 $traintypesql ORDER BY moduserdata.Organization ASC") or DIE($dbcon->ErrorMsg());
   $groups_numRows=0;
   $groups__totalRows=$groups->RecordCount();
?>
<?php
   $Repeat2__numRows = -1;
   $Repeat2__index= 0;
   $groups_numRows = $groups_numRows + $Repeat2__numRows;
?>
<?php while (($Repeat2__numRows-- != 0) && (!$groups->EOF)) 
   { 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td colspan="2"><p class="text"><b><?php echo $groups->Fields("FirstName")."&nbsp;".$groups->Fields("LastName")."&nbsp;&nbsp;".$groups->Fields("Organization");?>
<?php if (($groups->Fields("City") !=($null))  && ($groups->Fields("state") !=($null))) {?>
	 <br><?php echo $groups->Fields("City")?>, <?php echo $groups->Fields("state")?></b></p> <?php }?>
	  </td>
  </tr>
  <tr> 
    <td width="5%">&nbsp;</td>
    <td><?php if (($groups->Fields("WebPage") != ($null)) and ($groups->Fields("WebPage") != ("http://")))  { ?>
	<a href="<?php echo $groups->Fields("WebPage")?>"><?php echo $groups->Fields("WebPage")?></a><br><?php }?>
<?php if ( ($groups->Fields("EmailAddress") !=($null)) or ($groups->Fields("Phone") !=($null))) {?>	
	<strong>Contact:</strong>&nbsp;<a href="mailto:<?php echo $groups->Fields("EmailAddress")?>"><?php echo $groups->Fields("EmailAddress")?></a>&nbsp;<?php echo $groups->Fields("Phone")?><br>
<?php }
if ($groups->Fields("field1") != ($null)) { echo "<b>Speakers Issues:</b> ".$groups->Fields("field1")."<br>";}
if ($groups->Fields("field2") != ($null)) { echo "<b>Biographical Information:</b> ".$groups->Fields("field2")."<br>";}
if ($groups->Fields("field3") != ($null)) { echo "<b>Travel or Speaking Stipend Requested:</b> ".$groups->Fields("field3")."<br>";}
if ($groups->Fields("notes") != ($null)) { echo "<b>Other Information:</b> ".$groups->Fields("notes");}



?>
</p></td>
  </tr>
</table>
<br>
<?php
  $Repeat2__index++;
  $groups->MoveNext();
}
?>
<?php
  $groups->Close();
?>
<?php


  $Repeat1__index++;
  $area->MoveNext();
}


  $area->Close();
?>

<?php include("AMP/BaseFooter.php"); ?>