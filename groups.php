<?php 

/*********************
05-06-2003  v3.01
Module:  Groups
Description:  displays all groups sorted by city and state
CSS: text, title, eventtitle, bodygrey, eventsubtitle, blueheading
GET VARS: area
Userdatamod vars: .custom3 =  national group
									custom4 =  student
									custom2 =  front page link
									customd5 = web publish
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


include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/Region.inc.php"); 

if (!$_REQUEST['in']) {
	$modinin =2; 
} else {
	$modinin = $_REQUEST['in'];
	if ($_REQUEST['modid']) {
		$modid = $_REQUEST['modid']; 
	}
	if ($_REQUEST['mod_id']) {
		$mod_id = $_REQUEST['mod_id']; 
	}
}

include("AMP/BaseModuleIntro.php"); 


if (!function_exists('str_split')) {
	function str_split($string, $chunksize=1) {
		preg_match_all('/('.str_repeat('.', $chunksize).')/Uims', $string, $matches);
		return $matches[1];
	}
} 

if (!function_exists('get_state_name')) {
	function get_state_name($st) {
		global $dbcon;
		if ( is_numeric($st) ) {
			$st = state_convert($st);
		} 
		$sql = "SELECT statename from states where state = '$st' ";
		if ($st) {
			$S=$dbcon->CacheExecute($sql) or DIE($sql.$dbcon->ErrorMsg());
			$state = $S->Fields("statename");
			
			return $state;
		}		
	}
}


if (!function_exists('get_country_name')) {
	function get_country_name($t) {		
		$r = new Region;
		$c = $r->regions['WORLD'][$t];		
		return $c;				
	}
}

function groups_error($debug = NULL) {
	echo '<p class="text">There are currently no local groups listed that match you request.</p>';//.$debug;
}

function group_title($t) {
	echo "<p class=\"title\">$t</p>\n";
}
 

function group_cap_title($t) {
	echo "<p class=\"title\">".strtoupper($t)."</p>\n";
}


function group_subtitle($t) {
	echo "<p class=\"subtitle\">$t</p>\n";
}

function state_convert($in) {
	global $dbcon;
	if ( is_numeric($in) ) {

		$S=$dbcon->CacheExecute("SELECT state from states where id = $in ") or DIE($dbcon->ErrorMsg());
		$out = $S->Fields("state");
		return $out;
		
	} else {
		return $in;
	}
}



//Construct SQL

if ($_GET["area"]) {
    if ($_GET['area'] == '53') {
        $area_sql = " and u.Country != 'USA' ";
    } elseif (!$nonstateregion) { 
		$area_sql = " and u.State = '".state_convert($_GET["area"])."' ";
    } else {
		$area_sql = " and r.id = '".$_GET["area"]."'";
	}
}

$gsql =  "SELECT u.*, r.title as region FROM userdata u left join  region r on u.region=r.id  WHERE  u.publish = '1' and u.modin= $modinin $area_sql  ";
if ($web_publish == 1){
	$gsql .= " and u.custom5 = 1 ";
}
if  ($grouptype == "student"){ 
	$gsql .= " and u.custom4 = 1 " ;
}
$gsqlo ="ORDER BY u.State, u.Company ASC";

//if (!$groupslayout) {
	$groupslayout="groups.layout.php";
//}
if ($_REQUEST['gdisplay']) {	
	$gdisplay = $_REQUEST['gdisplay'] ;
}
#if  ($_GET["area"]) {
#	$gdisplay = NULL;
#}

######################## SET AREA #################


#########DISPLAY FOR INTERNATIONAL LISTING ##########################
function groups_intl($gsql,$gsqo=NULL)  {
	global $nonstateregion, $groupslayout, $dbcon;
	$gsqlo =" and u.Country != 'USA' ORDER BY u.Country, u.State , u.City, u.Company asc";
	$groups=$dbcon->CacheExecute( $gsql."  $gsqlo ") or DIE($gsql.$dbcon->ErrorMsg());
	if (!$groups->RecordCount() ){
		 //echo groups_error();
	}
	$currentName = '';
	$currentState = '';
	$currentCity = '';
	while (!$groups->EOF) { 
		if ($groups->Fields("Country") != $currentCountry) {
			echo group_cap_title(get_country_name($groups->Fields("Country")));
		}
		if ( ( get_state_name($groups->Fields("State")) != $currentState )  && ( $groups->Fields("State") != 'Intl') ) {
			echo group_title(get_state_name($groups->Fields("State")));
		}
		if ($groups->Fields("City") != $currentCity){
			echo group_subtitle($groups->Fields("City"));
			$currentCountry = trim($groups->Fields("Country")); 
			$currentState = get_state_name($groups->Fields("State")); 
			$currentCity = trim($groups->Fields("City")); 
			include ("$groupslayout");
		}
		$groups->MoveNext(); 
	}
}

#########DISPLAY FOR ALPHA LISTING with links ##########################

function groups_alpha($gsql,$gsqo=NULL)  {
	global $nonstateregion, $groupslayout, $dbcon;
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
		$groups=$dbcon->CacheExecute($gsql." and u.Company like '$v%'  $gsqlo ") or DIE($dbcon->ErrorMsg());  
  		if ($groups->Fields("id")) {
			echo "<a name=$v></a>".group_title("-".$v."-");
			while (!$groups->EOF){ 
				include ("$groupslayout");
				$groups->MoveNext();
			}
 		}
		$i++;
   	}
}


#########DISPLAY FOR ALPHA  ##########################
function groups_alphalist($gsql,$gsqlo){
	global $nonstateregion, $groupslayout, $dbcon;
	$groups=$dbcon->CacheExecute("$gsql $gsqlo ") or DIE($dbcon->ErrorMsg());  
	if ( !$groups->RecordCount() ){ echo groups_error(); }
	while (!$groups->EOF) { 
		include ("$groupslayout");
		$groups->MoveNext();
	}
}


######### Groups Details ##########################
function groups_details($id,$modinin) {
	global $dbcon,$groupslayout;
	$sql = "Select * from userdata where publish =1 and modin= $modinin and id = $id";
	$groups=$dbcon->CacheExecute($sql) or DIE($sql.$dbcon->ErrorMsg());  
	include ("$groupslayout");

}


#########DISPLAY by custom field ##########################
function groups_custom($gsql,$gsqo=NULL,$modinin) {
	global $nonstateregion, $groupslayout, $dbcon;
	$fieldtitle=$dbcon->CacheExecute("SELECT  ".$_GET['field']."text  as title from  userdata_fields where id = $modinin ") or DIE($dbcon->ErrorMsg());  
	echo group_title($fieldtitle->Fields("title"));
	$groups=$dbcon->CacheExecute($gsql." and ".$_GET['field']." = 1  $gsqlo ") or DIE($dbcon->ErrorMsg());  
	if (!$groups->RecordCount() ){ echo groups_error(); }
	while (!$groups->EOF){ 
		include ("$groupslayout");
		$groups->MoveNext();
	}
}

############DISPLAY BY  REGION AND THEN State and CITY################################################

function groups_state_city($gsql,$gsqo=NULL)   {
	global $nonstateregion, $groupslayout, $dbcon;
	if ($nonstateregion) { 
		$gsqlo =" and u.Country = 'USA'  ORDER BY u.Country desc, r.title asc, u.City asc, u.Company asc ";
	} else {
		$gsqlo =" and u.Country = 'USA' ORDER BY u.Country desc, u.State asc, u.City asc, u.Company asc ";
	}
	$groups=$dbcon->CacheExecute($gsql."   $gsqlo") or DIE("Error in function groups_state_city".$gsql.$dbcon->ErrorMsg());
    if ($groups->Fields("Country") != $currentCountry) {
         echo group_cap_title(get_country_name($groups->Fields("Country")));
        }	
    if (!$groups->RecordCount() ){ echo groups_error($gsql.$gsqlo); }
	$currentRegion = '';
	$currentState = '';
	$currentCity = '';
	while (!$groups->EOF) { 
		if (!$nonstateregion) {   
			$Areaname = get_state_name($groups->Fields("State"));
		} else { 
			$Areaname = $groups->Fields("region");
		}
		if ($Areaname != $currentArea ) {
			echo group_title($Areaname);
		}
		if ($groups->Fields("City") != $currentCity) {
			echo group_subtitle($groups->Fields("City"));
		}
		$currentArea = trim($Areaname); 
		$currentCity = trim($groups->Fields("City")); 
		include ($groupslayout);
		$groups->MoveNext(); 
	}
}

############DISPLAY BY  REGION AND THEN State################################################

function groups_state($gsql,$gsqo=NULL)   {
	global $nonstateregion, $groupslayout, $dbcon;
	 if ($groups->Fields("Country") != $currentCountry) {
         echo group_cap_title(get_country_name($groups->Fields("Country")));
        }	

	if ($nonstateregion) { 
		$gsqlo =" and u.Country = 'USA' ORDER BY u.Country desc, r.title asc,  u.Company asc ";
	} else {
		$gsqlo ="and u.Country = 'USA' ORDER BY u.Country desc, u.State asc,  u.Company asc ";
	}
	$groups=$dbcon->CacheExecute($gsql."  $gsqlo") or DIE("Error in function groups_state".$gsql.$gsqlo.$dbcon->ErrorMsg());
	if (!$groups->RecordCount() ){ echo groups_error(); }
	$currentRegion = '';
	$currentState = '';
	$currentCity = '';
	while (!$groups->EOF) { 
		if (!$nonstateregion) {   
			$Areaname = get_state_name($groups->Fields("State"));
		} else { 
			$Areaname = $groups->Fields("region");
		}
		if ($Areaname != $currentArea ) {
			echo group_title($Areaname);
		}
		$currentArea = trim($Areaname); 
		include ($groupslayout);
	
	$groups->MoveNext(); 
	}
}

##start display

if ($_GET['gid']) {
	groups_details($_GET['gid'],$modinin);
}

elseif ($gdisplay == 2 ) {
	groups_intl($gsql,$gsqo);
}
elseif ($gdisplay == 3) {
	groups_alpha($gsql,$gsqlo);
}
elseif ($gdisplay == 4) {
	groups_alphalist($gsql,$gsqlo);
}
elseif ($gdisplay == 5) {
    groups_intl($gsql,$gsqo);
    if ($_GET['area'] != 53) {
	    groups_state_city($gsql,$gsqo);
    }
}
elseif ($gdisplay == 6) {
	groups_custom($gsql,$gsqo,$modinin);
}
elseif ($gdisplay == 7) {
	groups_intl($gsql,$gsqo);

    if ($_GET['area'] != 53) {
	    groups_state($gsql,$gsqo);
    }
}

else {
	groups_state($gsql,$gsqo);
}

#############################################################
 include("AMP/BaseFooter.php"); ?>
