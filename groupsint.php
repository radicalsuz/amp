<?php
/* disabled due to disuse and security concerns
 * ap 2008-07
 */
/*********************
12-15-2003  v3.01
Module:  Groups
Description:  displays all groups sorted by city and state and country and sector
CSS: text, title, eventtitle, bodygrey, eventsubtitle, blueheading
GET VARS: area, sector
Userdatamod vars: .field3 =  national group
									field4 =  student
									field2 =  front page link
									fieldd5 = web publish
VARS - $web_publish
To Do:  write better sql
				search box

*********************/ 

/*
 
$modid = 5;
$mod_id = 58;
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php"); ?>
<?php
if ($web_publish == 1){
$webok = "and moduserdata.field5 = 1";}


if  ((isset($HTTP_GET_VARS["area"])) && (($HTTP_GET_VARS["area"]) != "53") or (isset($HTTP_GET_VARS["sector"])) ) {
if  ($grouptype == "student"){ $sqltype = "and moduserdata.field4 = 1" ;}
if (isset($HTTP_GET_VARS["area"])) {$areacalled = "and moduserdata.State = ".$HTTP_GET_VARS["area"];}
if (isset($HTTP_GET_VARS["sector"])) {$sectorcalled = "and  states.sector = ".$HTTP_GET_VARS["sector"];}
$area=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states inner join moduserdata on states.id=moduserdata.State where moduserdata.publish=1 and moduserdata.modinid=2  $areacalled  $sectorcalled and  moduserdata.field3 != 1 $webok $sqltype Order by states.statename asc") or DIE($dbcon->ErrorMsg());

while  (!$area->EOF)
   { 
?>  
<p class="title">
  <a name="<?php echo $area->Fields("id")?>"></a><?php echo $area->Fields("statename")?> 
   </p>
<?php
   $MM_linkid=$area->Fields("id");
   $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state FROM moduserdata left join states on states.id=moduserdata.State WHERE moduserdata.State =$MM_linkid and moduserdata.publish = '1' and moduserdata.modinid=2 and moduserdata.field3 != 1  $webok ORDER BY moduserdata.Organization ASC") or DIE($dbcon->ErrorMsg());

 while  (!$groups->EOF)
   { 
?>
<span class ="eventtitle"><a <?php 
 if (($groups->Fields("WebPage") != ($null)) and ($groups->Fields("WebPage") != ("http://")))  
 {echo "href=\"".$groups->Fields("WebPage")."\"";}?> class ="eventtitle"><?php echo $groups->Fields("Organization")?></a><br>
<?php if (($groups->Fields("City") !=($null))  && ($groups->Fields("state") !=($null))) {?>
	 <span class="eventsubtitle"><?php echo $groups->Fields("City")?>, <?php  if  ($groups->Fields("state") ==53) {echo $groups->Fields("Country") ;} else { 
	 echo $groups->Fields("state");}?></span><br><?php }?>
	 
<?php if (($groups->Fields("FirstName") !=($null))  or ($groups->Fields("LastName") !=($null))) { ?>
<span class="bodygrey"><?php echo $groups->Fields("FirstName")?>&nbsp;<?php echo $groups->Fields("LastName")?></span><br><?php } ?>
<?php if (($groups->Fields("EmailAddress") !=($null)) ) { ?>
<span class="bodygrey"><a href="mailto:<?php echo $groups->Fields("EmailAddress")?>"><?php echo $groups->Fields("EmailAddress")?></a></span><br><?php } ?>
<?php if (($groups->Fields("Phone") !=($null)) ) { ?>
<span class="bodygrey"><?php echo $groups->Fields("Phone")?></span><br><?php } ?>
<?php if ($groups->Fields("field1") != ($null)) { ?>
<span class="text"><?php echo converttext($groups->Fields("field1")); ?></span><br><?php }?>
<br>
<?php
    $groups->MoveNext();
}
  $area->MoveNext();
}

if  ((isset($HTTP_GET_VARS["area"])) && (($HTTP_GET_VARS["area"]) != "53") or (isset($HTTP_GET_VARS["sector"])) ) {
if ($area->RecordCount() == 0){ //start failed area called
echo "<span class=\"title\">".$area->Fields("statename")."</span>";?>
<p class="text">There are currently no local groups listed in this area.</p>
<?php }//end failed area called
 } //end areacalled 
}


else {

 if  (($HTTP_GET_VARS["area"] == NULL) ||  ($HTTP_GET_VARS["area"] != "53") || ($HTTP_GET_VARS["sector"] == NULL)) {
 ##SELECT COUNTRY (US AND CANADA)
 
    $dbcountry=$dbcon->CacheExecute("SELECT distinct Country FROM moduserdata where publish=1 and (Country= 'United States' or Country= 'Canada')  and modinid=2   Order by Country  desc") or DIE($dbcon->ErrorMsg());


while (!$dbcountry->EOF)
   { 
    $Country=   $dbcountry->Fields("Country");?>
   <p class="title" > <?php echo $dbcountry->Fields("Country")?> </p>
   <?php
    $area=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states inner join moduserdata on states.id=moduserdata.State where moduserdata.publish=1 and moduserdata.modinid=2 and  moduserdata.Country = '$Country' and moduserdata.field3 != 1 $webok Order by states.statename asc") or DIE($dbcon->ErrorMsg());
?>  		
<?php while (!$area->EOF)
   { 
?>  
<p class="title">
  <a name="<?php echo $area->Fields("id")?>"></a><?php echo $area->Fields("statename")?> 
   </p>
<?php
   $MM_linkid=$area->Fields("id");
   $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state FROM moduserdata left join states on states.id=moduserdata.State WHERE moduserdata.State =$MM_linkid and moduserdata.publish = '1' and moduserdata.modinid=2 and moduserdata.field3 != 1  $webok ORDER BY moduserdata.Organization ASC") or DIE($dbcon->ErrorMsg());

 while  (!$groups->EOF)
   { 
?>
<span class ="eventtitle"><a <?php 
 if (($groups->Fields("WebPage") != ($null)) and ($groups->Fields("WebPage") != ("http://")))  
 {echo "href=\"".$groups->Fields("WebPage")."\"";}?> class ="eventtitle"><?php echo $groups->Fields("Organization")?></a><br>
<?php if (($groups->Fields("City") !=($null))  && ($groups->Fields("state") !=($null))) {?>
	 <span class="eventsubtitle"><?php echo $groups->Fields("City")?>, <?php  if  ($groups->Fields("state") ==53) {echo $groups->Fields("Country") ;} else { 
	 echo $groups->Fields("state");}?></span><br><?php }?>
	 
<?php if (($groups->Fields("FirstName") !=($null))  or ($groups->Fields("LastName") !=($null))) { ?>
<span class="bodygrey"><?php echo $groups->Fields("FirstName")?>&nbsp;<?php echo $groups->Fields("LastName")?></span><br><?php } ?>
<?php if (($groups->Fields("EmailAddress") !=($null)) ) { ?>
<span class="bodygrey"><a href="mailto:<?php echo $groups->Fields("EmailAddress")?>"><?php echo $groups->Fields("EmailAddress")?></a></span><br><?php } ?>
<?php if (($groups->Fields("Phone") !=($null)) ) { ?>
<span class="bodygrey"><?php echo $groups->Fields("Phone")?></span><br><?php } ?>
<?php if ($groups->Fields("field1") != ($null)) { ?>
<span class="text"><?php echo converttext($groups->Fields("field1")); ?></span><br><?php }?>
<br>
<?php
    $groups->MoveNext();
}

  $area->MoveNext();
}

  $dbcountry->MoveNext();
}

}
}
if  (($HTTP_GET_VARS["area"] == NULL) || ($HTTP_GET_VARS["area"] == "53")  ) {
if ($HTTP_GET_VARS["sector"] == NULL) {
##OTHER COUNTRIES

   $dbcountry2=$dbcon->CacheExecute("SELECT distinct Country FROM moduserdata where publish=1  and modinid=2   Order by Country  asc") or DIE($dbcon->ErrorMsg());


while (!$dbcountry2->EOF)
   {
    $Country=   $dbcountry2->Fields("Country");
   if  ($Country =='United States'  || $Country== 'Canada') {}
   else{
    ?>
   <p class="title" > <?php echo $Country ;?> </p>
   <?php

   $groups=$dbcon->CacheExecute("SELECT moduserdata.* FROM moduserdata  WHERE   moduserdata.Country= '$Country' and moduserdata.publish = '1' and moduserdata.modinid=2  $webok ORDER BY moduserdata.Organization ASC") or DIE($dbcon->ErrorMsg());

 while  (!$groups->EOF)
   { 
?>
<span class ="eventtitle"><a <?php 
 if (($groups->Fields("WebPage") != ($null)) and ($groups->Fields("WebPage") != ("http://")))  
 {echo "href=\"".$groups->Fields("WebPage")."\"";}?> class ="eventtitle"><?php echo $groups->Fields("Organization")?></a><br>
<?php if (($groups->Fields("City") !=($null))  ) {?>
	 <span class="eventsubtitle"><?php echo $groups->Fields("City")?></span><br><?php }?>
	 
<?php if (($groups->Fields("FirstName") !=($null))  or ($groups->Fields("LastName") !=($null))) { ?>
<span class="bodygrey"><?php echo $groups->Fields("FirstName")?>&nbsp;<?php echo $groups->Fields("LastName")?></span><br><?php } ?>
<?php if (($groups->Fields("EmailAddress") !=($null)) ) { ?>
<span class="bodygrey"><a href="mailto:<?php echo $groups->Fields("EmailAddress")?>"><?php echo $groups->Fields("EmailAddress")?></a></span><br><?php } ?>
<?php if (($groups->Fields("Phone") !=($null)) ) { ?>
<span class="bodygrey"><?php echo $groups->Fields("Phone")?></span><br><?php } ?>
<?php if ($groups->Fields("field1") != ($null)) { ?>
<span class="text"><?php echo converttext($groups->Fields("field1")); ?></span><br><?php }?>
<br>
<?php
    $groups->MoveNext();
}
}
  $dbcountry2->MoveNext();
}
  
}}


include("AMP/BaseFooter.php");
*/
?>
