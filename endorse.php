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
$modin = ($_REQUEST['modin'] && isset($_REQUEST['modin'])) ? $_REQUEST['modin']:1;
$mod_id = ($_REQUEST['modtext'] && isset($_REQUEST['modtext'])) ? $_REQUEST['modtext']:10;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  
 
 
#clean up from udm conversion
#also needs austin's instance code

#this area stuff is old and will need to be rewritten
#$areacalled= $_GET["area"];
#if  (($_GET["area"]) && ($_GET["area"] != "national")) {
#	$areaq=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states inner join moduserdata on states.id=moduserdata.State where moduserdata.publish=1 and moduserdata.modinid=1 and moduserdata.State = $areacalled and  moduserdata.field3 != 1 Order by states.statename asc") or DIE($dbcon->ErrorMsg());
#}

#elseif  (($_GET["area"]) != "national") {
#   $areaq=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states, moduserdata where  states.id=moduserdata.State and  moduserdata.publish=1 and moduserdata.modinid=1  Order by states.statename asc") or DIE($dbcon->ErrorMsg());
#}

#$MM_linkid=$areaq->Fields("id"); 

if (!

$groups=$dbcon->CacheExecute("SELECT * FROM userdata  WHERE  publish = '1' and $modin and custom1 = 1 ORDER BY moduserdata.Comapny ASC") or DIE($dbcon->ErrorMsg());

while (!$groups->EOF)   { 
	echo '<span class ="eventtitle"><a ';
	if (($groups->Fields("Web_Page") != (NULL)) and ($groups->Fields("Web_Page") != ("http://")))  {
		echo "href=\"".$groups->Fields("Web_Page")."\"";
	}
	echo ' class ="eventtitle" target="_blank">'.  $groups->Fields("Company")  .  '</a></span><br>';
	if (($groups->Fields("City") !=(NULL))  && ($groups->Fields("State") !=(NULL))) {
		echo '<span class="eventsubtitle">'  .  $groups->Fields("City") . ', ' . $groups->Fields("State") . '</span><br>';
	}
	if (($groups->Fields("First_Name") !=(NULL))  or ($groups->Fields("Last_Name") !=(NULL))) { 
		echo '<span class="bodygrey">'  .  $groups->Fields("First_Name") . '&nbsp;' . $groups->Fields("Last_Name") . '</span><br>';
	}
	if (($groups->Fields("Email") !=(NULL)) ) { 
		echo '<span class="bodygrey"><a href="mailto:' . $groups->Fields("Email") . '">' . $groups->Fields("Email")  . '</a></span><br>';
	 } 
	if (($groups->Fields("Phone") !=(NULL)) ) { 
		echo '<span class="bodygrey">' . $groups->Fields("Phone") . '</span><br>';
	} 
	echo '<br>';
	$groups->MoveNext();
}

include("AMP/BaseFooter.php");
?>