<?php
  require_once("../adodb/toexport2.inc.php");
  require_once("../adodb/adodb.inc.php");
  require_once("Connections/freedomrising.php");
  require_once("$ConfigPath2");


$bval= $_GET[id];
$R=$dbcon->Execute("SELECT * from userdata_fields where id=$bval") or DIE($dbcon->ErrorMsg());
$file = ereg_replace ("'", "" ,$R->Fields('name'));
$file = ereg_replace (",", "" ,$file);
$file = ereg_replace (" ", "_" ,$file);
$filename= $file.'.csv';
function setfname($name) {
	global $R;
	$namee= 'enabled_'.$name;
	$namet= 'type_'.$name;
	$namel = 'label_'.$name;
	if (($R->Fields($namee) == 1) && ($R->Fields($namee) != 'header')){ 
		$namel = ereg_replace ("'", "" ,$R->Fields($namel));
		$namel = ereg_replace (",", "" ,$namel);
		$output = ", " . $name . " as '".$namel."' "; 
	}
	return $output;
} 

for ($i = 1; $i <= 40; $i++) {
   	$varname = custom.$i; 
	$$varname = setfname('custom'.$i);
} 


$Title = setfname('Title');
$Last_Name = setfname('Last_Name');
$Suffix = setfname('Suffix');
$First_Name = setfname('First_Name');
$MI = setfname('MI');
$Company = setfname('Company');
$Notes = setfname('Notes');
$Email = setfname('Email');
$Phone = setfname('Phone');
$Cell_Phone = setfname('Cell_Phone');
$Phone_Provider = setfname('Phone_Provider');
$Work_Phone = setfname('Work_Phone');
$Pager = setfname('Pager');
$Home_Fax = setfname('Home_Fax');
$Web_Page = setfname('Web_Page');
$Street = setfname('Street');
$City = setfname('City');
$State = setfname('State');
$Zip = setfname('Zip');
$Country = setfname('Country');
$Work_Fax = setfname('Work_Fax');
$Street_2 = setfname('Street_2');
$Street_3 = setfname('Street_3');
$occupation = setfname('occupation');


$sql= "select distinct ";
$sql .= "id, publish $Title $Last_Name $Suffix $First_Name $MI $Company $Notes $Email $Phone $Cell_Phone $Phone_Provider $Work_Phone $Pager $Home_Fax $Web_Page $Street $City $State $Zip $Country $Work_Fax $Street_2 $Street_3 $occupation  ";
$sql .=" $custom1 $custom2 $custom3 $custom4 $custom5 $custom6 $custom7 $custom8 $custom9 $custom10 $custom11 $custom12 $custom13 $custom14 $custom15 $custom16 $custom17 $custom18 $custom19 $custom20 $custom21 $custom22 $custom23 $custom24 $custom25 $custom26 $custom27 $custom28 $custom29 $custom40 $custom30 $custom31 $custom32 $custom33 $custom34 $custom35 $custom36 $custom37 $custom38 $custom39 $custom40";
if (isset($_POST['sqlsend'])) {
	$sql .=stripslashes($_POST['sqlsend']);
} else {
	$sql .= " from userdata  where modin=$bval  ";
}
#echo $sql;
$db = &NewADOConnection('mysql');
$db->Connect($MM_HOSTNAME, $MM_USERNAME, $MM_PASSWORD, $MM_DATABASE);
//echo $sql."<br>";
$rs = $db->Execute($sql) or DIE($dbcon->ErrorMsg());
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=$filename");
print rs2csv($rs); # return a string, CSV formatprint '<hr>';?>