<?php  
/*********************
05-06-2003  v3.01
Module:  Endorse
Description:  display page for endorsements
CSS: eventtitle, eventsubtitle,  bodygrey
Userdatamod vars: .field1 =  show on page
									field3 =  national
Get  Vars: area  -if set  shows only endorsements in that area
To Do:  make modular so that  it can be passed  data from new modules
				write a better sql statement
*********************/ 

$modid = 6;
$mod_id = 10;
include("sysfiles.php");
include("header.php"); 
 
 $areacalled= $_GET["area"];
if  (($_GET["area"]) && ($_GET["area"] != "national")) {
$areaq=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states inner join moduserdata on states.id=moduserdata.State where moduserdata.publish=1 and moduserdata.modinid=1 and moduserdata.State = $areacalled and  moduserdata.field3 != 1 Order by states.statename asc") or DIE($dbcon->ErrorMsg());
}

elseif  (($_GET["area"]) != "national") {
   $areaq=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states, moduserdata where  states.id=moduserdata.State and  moduserdata.publish=1 and moduserdata.modinid=1  Order by states.statename asc") or DIE($dbcon->ErrorMsg());
   }

   $MM_linkid=$areaq->Fields("id");
   $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state FROM moduserdata left join states on moduserdata.State= states.id   WHERE  moduserdata.publish = '1' and moduserdata.modinid=1 and moduserdata.field1 = 1 ORDER BY moduserdata.Organization ASC") or DIE($dbcon->ErrorMsg());

 while (!$groups->EOF)   { 
?>
<span class ="eventtitle"><a <?php 
 if (($groups->Fields("WebPage") != ($null)) and ($groups->Fields("WebPage") != ("http://")))  
 {echo "href=\"".$groups->Fields("WebPage")."\"";}?> class ="eventtitle" target="_blank"><?php echo $groups->Fields("Organization")?></a></span><br>
<?php if (($groups->Fields("City") !=($null))  && ($groups->Fields("state") !=($null))) {?>
	 <span class="eventsubtitle"><?php echo $groups->Fields("City")?>, <?php echo $groups->Fields("state")?></span><br><?php }?>
	 
<?php if (($groups->Fields("FirstName") !=($null))  or ($groups->Fields("LastName") !=($null))) { ?>
<span class="bodygrey"><?php echo $groups->Fields("FirstName")?>&nbsp;<?php echo $groups->Fields("LastName")?></span><br><?php } ?>
<?php if (($groups->Fields("EmailAddress") !=($null)) ) { ?>
<span class="bodygrey"><a href="mailto:<?php echo $groups->Fields("EmailAddress")?>"><?php echo $groups->Fields("EmailAddress")?></a></span><br><?php } ?>
<?php if (($groups->Fields("Phone") !=($null)) ) { ?>
<span class="bodygrey"><?php echo $groups->Fields("Phone")?></span><br><?php } ?>

<br>
<?php

  $groups->MoveNext();
}
include("footer.php"); ?>
