<?php

require_once("AMP/BaseDB.php");
require_once("adodb/toexport2.inc.php");

$filename='dump.csv';
$bval= $_GET['id'];
$Recordset1=$dbcon->Execute("SELECT * from modfields where id=$bval") or die($dbcon->ErrorMsg());
if ($Recordset1->Fields("field1text") != NULL){ $field1 = ", moduserdata.field1 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field1text"))."' "; }
if ($Recordset1->Fields("field2text") != NULL){ $field2 = ", moduserdata.field2 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field2text"))."' "; }
if ($Recordset1->Fields("field3text") != NULL){ $field3 = ", moduserdata.field3 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field3text"))."' "; }
if ($Recordset1->Fields("field4text") != NULL){ $field4 = ", moduserdata.field4 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field4text"))."' "; }
if ($Recordset1->Fields("field5text") != NULL){ $field5 = ", moduserdata.field5 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field5text"))."' "; }
if ($Recordset1->Fields("field6text") != NULL){ $field6 = ", moduserdata.field6 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field6text"))."' "; }
if ($Recordset1->Fields("field7text") != NULL){ $field7 = ", moduserdata.field7 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field7text"))."' "; }
if ($Recordset1->Fields("field8text") != NULL){ $field8 = ", moduserdata.field8 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field8text"))."' "; }
if ($Recordset1->Fields("field9text") != NULL){ $field9 = ", moduserdata.field9 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field9text"))."' "; }
if ($Recordset1->Fields("field10text") != NULL){ $field10 = ", moduserdata.field10 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field10text"))."' "; }
if ($Recordset1->Fields("field11text") != NULL){ $field11 = ", moduserdata.field11 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field11text"))."' "; }
if ($Recordset1->Fields("field12text") != NULL){ $field12 = ", moduserdata.field12 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field12text"))."' "; }
if ($Recordset1->Fields("field13text") != NULL){ $field13 = ", moduserdata.field13 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field13text"))."' "; }
if ($Recordset1->Fields("field14text") != NULL){ $field14 = ", moduserdata.field14 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field14text"))."' "; }
if ($Recordset1->Fields("field15text") != NULL){ $field15 = ", moduserdata.field15 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field15text"))."' "; }
if ($Recordset1->Fields("field16text") != NULL){ $field16 = ", moduserdata.field16 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field16text"))."' "; }
if ($Recordset1->Fields("field17text") != NULL){ $field17 = ", moduserdata.field17 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field17text"))."' "; }
if ($Recordset1->Fields("field18text") != NULL){ $field18 = ", moduserdata.field18 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field18text"))."' "; }
if ($Recordset1->Fields("field19text") != NULL){ $field19 = ", moduserdata.field19 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field19text"))."' "; }
if ($Recordset1->Fields("field20text") != NULL){ $field20 = ", moduserdata.field20 as '".ereg_replace ("'", "" ,$Recordset1->Fields("field20text"))."' "; }

$sql= "select distinct ";
$sql .= "moduserdata.Organization, moduserdata.FirstName, moduserdata.LastName,  moduserdata.EmailAddress, moduserdata.Phone, moduserdata.Fax,    moduserdata.Address,  moduserdata.Address2,   moduserdata.City,  states.state,  moduserdata.PostalCode, moduserdata.Country, moduserdata.WebPage ";
$sql .=" $field1 $field2 $field3 $field4 $field5 $field6 $field7 $field8 $field9 $field10 $field11 $field12 $field13 $field14 $field15 $field16 $field17 $field18 $field19 $field20 ";
//$sql .= " moduserdata.field1 as '$field1', moduserdata.field2 as '$field2', moduserdata.field3 as '$field3', moduserdata.field4 as '$field4',  moduserdata.field5 as '$field5', moduserdata.field6 as '$field6', moduserdata.field7 as '$field7', moduserdata.field8 as '$field8', moduserdata.field9 as '$field9',   moduserdata.field10 as '$field10'";
$sql .= " from moduserdata left join states on moduserdata.State=states.id where modinid=$bval  ";
//echo $sql."<br>";
$rs = $dbcon->Execute($sql);
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$filename");
print rs2csv($rs); # return a string, CSV formatprint '<hr>';

?>
