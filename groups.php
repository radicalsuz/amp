<?php 
/*********************
05-06-2003  v3.01
Module:  Groups
Description:  displays all groups sorted by city and state
CSS: text, title, eventtitle, bodygrey, eventsubtitle, blueheading
GET VARS: area
Userdatamod vars: .field3 =  national group
									field4 =  student
									field2 =  front page link
									fieldd5 = web publish
VARS - $web_publish , $groupslayout, $gdisplay $groupslayout
$gdisplay 					1 = by region/state
									2 = International
									3 =  Alpha with letter links
									4 = Alpha 
									5 = by state and city (US and Canada Only)
									6 = by custom field
									7= by region and State and city (US and Canada Only)

To Do:  write better sql
				search box
				

*********************/ 

$modid = 5;
$mod_id = 58;

//include("includes/base.php");
//include("includes/moduleintro.php");  
include("sysfiles.php");

if (!$_REQUEST[in]) {$modinin =2; }
else {$modinin = $_REQUEST[in];
if ($_REQUEST[modid]) {$modid = $_REQUEST[modid]; }
if ($_REQUEST[mod_id]) {$mod_id = $_REQUEST[mod_id]; }
}
include("header.php"); 


//Construct SQL
$gsql =  " moduserdata.publish = '1' and moduserdata.modinid= $modinin   " ;
if ($web_publish == 1){
$gsql = $gsql." and moduserdata.field5 = 1 ";}
if  ($grouptype == "student"){ $gsql = $gsql." and moduserdata.field4 = 1 " ;}

$gsqlo ="ORDER BY moduserdata.Organization ASC";

if (!$groupslayout) {$groupslayout="groups.layout.php";}
if ($_REQUEST[gdisplay]) {$gdisplay = $_REQUEST[gdisplay] ;}
if  ($_GET["area"]) {$gdisplay = NULL;}
######################## SET AREA #################
$areacalled__MMColParam = $_GET["area"];
if  (($_GET["area"]) && ($_GET["area"] != "national")) {

  if (!$nonstateregion ) {
$areaq=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states, moduserdata   where $gsql and states.id=moduserdata.State  and  moduserdata.State = $areacalled__MMColParam   and moduserdata.field3 != 1 Order by states.id asc") or DIE($dbcon->ErrorMsg());}
else {
$areaq=$dbcon->CacheExecute("SELECT distinct region.id, region.title as statename, moduserdata.region FROM region, moduserdata   where $gsql and region.id=moduserdata.region  and moduserdata.region = $areacalled__MMColParam and  moduserdata.field3 != 1 Order by region.title asc") or DIE($dbcon->ErrorMsg());}

}

elseif  (($_GET["area"]) != "national") {
 if (!$nonstateregion) {
   $areaq=$dbcon->CacheExecute("SELECT distinct states.*, moduserdata.State FROM states, moduserdata   where $gsql and states.id=moduserdata.State  and moduserdata.field3 != 1  and  states.id != 53 Order by states.id asc") or DIE($dbcon->ErrorMsg());
   }
  else {
$areaq=$dbcon->CacheExecute("SELECT distinct region.id, region.title as statename, moduserdata.region FROM region, moduserdata   where $gsql and region.id=moduserdata.region and   moduserdata.field3 != 1  Order by region.title asc") or DIE($dbcon->ErrorMsg());} 
   }

################ DISPLAY FAILURE ######################

 if  (isset($HTTP_GET_VARS["area"]) && ($HTTP_GET_VARS["area"] != "300")) { //start area called
if (!$areaq->RecordCount() ){ //start failed area called
echo "<span class=\"title\">".$areaq->Fields("statename")."</span>";?>
<p class="text">There are currently no local groups listed in this area.</p>
<?php }//end failed area called
 } //end areacalled 

#########DISPLAY FOR INTERNATIONAL LISTING ##########################
if ($gdisplay == 2) {
$gsqlo ="ORDER BY moduserdata.Country asc, states.statename asc, moduserdata.City asc, moduserdata.Organization asc ";
$groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, states.statename FROM moduserdata, states  WHERE $gsql and states.id=moduserdata.State  $gsqlo ") or DIE($dbcon->ErrorMsg());
$currentName = '';
$currentState = '';
$currentCity = '';
   while (!$groups->EOF)
   { 
	if ($groups->Fields("Country") != $currentCountry) echo '<a name= "'. $groups->Fields("Country") .'"></a><p class= title>'. $groups->Fields("Country") .'</p>';
	if ($groups->Fields("statename") != $currentState  && $groups->Fields("state") != 'Intl' ) echo '<h2>'. $groups->Fields("statename") .'</h2>';
	if ($groups->Fields("City") != $currentCity) echo '<p class= subtitle>'. $groups->Fields("City") .'</p>';
	
	
				$currentCountry = trim($groups->Fields("Country")); 
				$currentState = $groups->Fields("statename"); 
				$currentCity = trim($groups->Fields("City")); 
				include ("$groupslayout");
		$groups->MoveNext(); }
 }

#########DISPLAY FOR ALPHA LISTING with links ##########################
elseif ($gdisplay == 3) {

if (!function_exists('str_split')) {
  function str_split($string, $chunksize=1) {
   preg_match_all('/('.str_repeat('.', $chunksize).')/Uims', $string, $matches);
   return $matches[1];
  }
} 
$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

$arr1 = str_split($str);
$i = 0;
$str2 ='';
foreach($arr1 as $v) {
$str2 .=   "<a href =\"#$v\"> $v  </a>";
 $i++;
   }
  $str2 .="<br/><br/>";
  echo $str2;
$arr1 = str_split($str);
$i = 0;
foreach($arr1 as $v) {

 $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region and Organization like '$v%'  $gsqlo ") or DIE($dbcon->ErrorMsg());  
  if ($groups->Fields(id)) {echo "<a name=$v></a><span class=\"title\">-$v-</span><br/><br/>";
 while (!$groups->EOF)
   { 
include ("$groupslayout");

  $groups->MoveNext();
}
 }

   $i++;
   }

}
#########DISPLAY FOR ALPHA  ##########################
elseif ($gdisplay == 4) {

 $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region   $gsqlo ") or DIE($dbcon->ErrorMsg());  
 
 while (!$groups->EOF)
   { 
include ("$groupslayout");

  $groups->MoveNext();
}

}


############DISPLAY BY  REGION AND THEN CITY################################################

 elseif ($gdisplay == 5) {
 
if (!$nonstateregion) {
$gsqlo ="ORDER BY moduserdata.Country desc, states.statename asc, moduserdata.City asc, moduserdata.Organization asc ";
$groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, states.statename FROM moduserdata, states  WHERE $gsql and states.id=moduserdata.State  and moduserdata.State != 53  $gsqlo ") or DIE($dbcon->ErrorMsg());}
 else {    
 $gsqlo ="ORDER BY moduserdata.Country desc, region.title asc, moduserdata.City asc, moduserdata.Organization asc ";
  $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region and moduserdata.State != 53   $gsqlo") or DIE($dbcon->ErrorMsg());   }



$currentArea = '';
$currentCity = '';
   while (!$groups->EOF) { 
   if (!$nonstateregion) {   $Areaname = $groups->Fields("statename");}
   else { $Areaname = $groups->Fields("title");}
  
	
	if ($Areaname != $currentArea ) echo '<p class= title>'. $Areaname .'</p>';
	if ($groups->Fields("City") != $currentCity) echo '<p class= subtitle>'. $groups->Fields("City") .'</p>';
	
	
				
				$currentArea = trim($Areaname); 
				$currentCity = trim($groups->Fields("City")); 
				include ("$groupslayout");
		$groups->MoveNext(); }
		}

 
 
 
 /*  while (!$areaq->EOF)
   { 
    $MM_linkid=$areaq->Fields("id");
		 if ($nonstateregion !=1) {
   $cgroups=$dbcon->CacheExecute("SELECT distinct moduserdata.city FROM moduserdata  WHERE $gsql  and moduserdata.State =$MM_linkid  and moduserdata.field3 != 1   order by city asc ") or DIE($dbcon->ErrorMsg());   }
   else {
      $cgroups=$dbcon->CacheExecute("SELECT distinct  moduserdata.city FROM moduserdata WHERE $gsql  and moduserdata.region =$MM_linkid and moduserdata.field3 != 1   order by city asc") or DIE($dbcon->ErrorMsg());   }
	

?>  
<p class="title">
  <a name="<?php echo $areaq->Fields("id")?>"></a><?php echo $areaq->Fields("statename")?> 
   </p>
<?php
    while (!$cgroups->EOF)
   {  
   $city_linkid=$cgroups->Fields("city");
   ?>
   <p class="subtitle">
  <?php echo $cgroups->Fields("city")?> 
   </p>
   <?php
	 if ($nonstateregion !=1) {
   $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region  and moduserdata.State =$MM_linkid  and moduserdata.field3 != 1 and moduserdata.city = '$city_linkid'   $gsqlo ") or DIE($dbcon->ErrorMsg());   }
   else {      $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region  and moduserdata.region =$MM_linkid and moduserdata.field3 != 1  and moduserdata.city = '$city_linkid'  $gsqlo") or DIE($dbcon->ErrorMsg());   }

while (!$groups->EOF)
   { 
include ("$groupslayout");
  $groups->MoveNext();
}
  $cgroups->MoveNext();
}
  $areaq->MoveNext();
}
if ( (empty($_GET["area"])) or ($HTTP_GET_VARS["area"]) == ("300")) {
   	 if ($nonstateregion !=1) {
   $ngroups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region   and moduserdata.field3 = 1   $gsqlo ") or DIE($dbcon->ErrorMsg());   }
   else {
      $ngroups=$dbcon->CacheExecute("sleECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region and moduserdata.field3= 1   $gsqlo") or DIE($dbcon->ErrorMsg());   }

if ($ngroups->RecordCount() ){
?>
<p class="blueheading"> <b>National Organizations</b> </p>
<?php
}
 while(!$ngroups->EOF)   { 
include ("$groupslayout");
  $ngroups->MoveNext(); }
}
} */
#########DISPLAY by custom field ##########################
elseif ($gdisplay == 6) {
$fieldtitle=$dbcon->CacheExecute("SELECT  ".$_GET[field]."text  as title from  modfields where id = $modinin ") or DIE($dbcon->ErrorMsg());  
echo "<p class =title>".$fieldtitle->Fields("title")."</p>";

 $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region and  $_GET[field] = 1  $gsqlo ") or DIE($dbcon->ErrorMsg());  
//echo "<span class=\"title\"></span><br/><br/>";
 while (!$groups->EOF)
   { 
include ("$groupslayout");

  $groups->MoveNext();

}
}

############DISPLAY BY  REGION AND THEN State and CITY################################################

 elseif ($gdisplay == 7) {
 
  
 $gsqlo ="ORDER BY moduserdata.Country desc, region.title asc, moduserdata.City asc, moduserdata.Organization asc ";
  $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region and moduserdata.State != 53   $gsqlo") or DIE($dbcon->ErrorMsg());   



$currentRegion = '';
$currentState = '';
$currentCity = '';
   while (!$groups->EOF) { 
   if (!$nonstateregion) {   $Areaname = $groups->Fields("statename");}
   else { $Areaname = $groups->Fields("title");}
  
	
	if ($Areaname != $currentArea ) echo '<p class= title>'. $Areaname .'</p>';
	if ($groups->Fields("City") != $currentCity) echo '<p class= subtitle>'. $groups->Fields("City") .'</p>';
	
	
				
				$currentArea = trim($Areaname); 
				$currentCity = trim($groups->Fields("City")); 
				include ("$groupslayout");
		$groups->MoveNext(); }
		}
###############DEFUALT LAYOUT##################################################################
else {
while (!$areaq->EOF)
   { 
?>  
<p class="title">
  <a name="<?php echo $areaq->Fields("id")?>"></a><?php echo $areaq->Fields("statename")?> 
   </p>
<?php
   $MM_linkid=$areaq->Fields("id");
	 if ($nonstateregion !=1) {
   $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region  and moduserdata.State =$MM_linkid  and moduserdata.field3 != 1   $gsqlo ") or DIE($dbcon->ErrorMsg());   }
   else {
      $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region  and moduserdata.region =$MM_linkid and moduserdata.field3 != 1   $gsqlo") or DIE($dbcon->ErrorMsg());   }

while (!$groups->EOF)
   { 
include ("$groupslayout");
  $groups->MoveNext();
}
  $areaq->MoveNext();
}

//NATIONAL GROUPS

if ( (empty($_GET["area"])) or ($_GET["area"]) == ("300")) {
   	 if ($nonstateregion !=1) {
   $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region and moduserdata.field3 = 1   $gsqlo ") or DIE($dbcon->ErrorMsg());   }
   else {
      $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state, region.title FROM moduserdata, states, region   WHERE $gsql and states.id=moduserdata.State and region.id= moduserdata.region  and moduserdata.field3= 1   $gsqlo") or DIE($dbcon->ErrorMsg());   }

if ($groups->RecordCount() ){
?>
<p class="blueheading"> <b>National Organizations</b> </p>
<?php
}
 while(!$groups->EOF)   { 
include ("$groupslayout");
  $groups->MoveNext(); }
}//end national
}

#############################################################
 include("footer.php"); ?>
