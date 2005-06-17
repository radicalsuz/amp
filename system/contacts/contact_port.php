<?php
set_time_limit(0);
 require("adodb/adodb.inc.php");
   $MM_HOSTNAME = "localhost";
  $MM_DBTYPE = "mysql";
 $MM_DATABASE = "seaflow";
  $MM_USERNAME = "david";
  $MM_PASSWORD = "havlan";
    ADOLoadCode($MM_DBTYPE);
   $dbcon=&ADONewConnection($MM_DBTYPE);
   $dbcon->Connect($MM_HOSTNAME,$MM_USERNAME,$MM_PASSWORD,$MM_DATABASE);


function adddef($type, $name, $camid, $order) {
global $dbcon;
   $createdef = $dbcon->Execute("INSERT INTO contacts_fields (type,name,camid,fieldorder)VALUES ('".$type."', '".$name."', '".$camid."', '".$order."')") or DIE($dbcon->ErrorMsg());
   }
   
   function addv($perid, $fieldid, $value) {
global $dbcon;
$value= addslashes($value);
        $createdef = $dbcon->Execute("INSERT INTO contacts_rel (fieldid,perid,value)VALUES ('".$fieldid."', '".$perid."', '".$value."')") or DIE($dbcon->ErrorMsg());
		echo $value."<br>";
}
function getfid($camid, $order) {
global $dbcon;
$getf=$dbcon->Execute("Select * from contacts_fields where camid = ".$camid." and fieldorder = $order ")or DIE($dbcon->ErrorMsg());
$fieldid = $getf->Fields("id");
return $fieldid;

}


 $getcam=$dbcon->Execute("SELECT * from campaigns") or DIE($dbcon->ErrorMsg());
 
   while  (!$getcam->EOF) { 
   //for each field where itis valid create the field definition
   if ($getcam->Fields("field1text")) {adddef($getcam->Fields("1ftype"), $getcam->Fields("field1text"),$getcam->Fields("id"), 1 );}
   if ($getcam->Fields("field2text")) {adddef($getcam->Fields("2ftype"), $getcam->Fields("field2text"),$getcam->Fields("id"), 2 );}
   if ($getcam->Fields("field3text")) {adddef($getcam->Fields("3ftype"), $getcam->Fields("field3text"),$getcam->Fields("id"), 3 );}
   if ($getcam->Fields("field4text")) {adddef($getcam->Fields("4ftype"), $getcam->Fields("field4text"),$getcam->Fields("id"), 4 );}
   if ($getcam->Fields("field5text")) {adddef($getcam->Fields("5ftype"), $getcam->Fields("field5text"),$getcam->Fields("id"), 5 );}
   if ($getcam->Fields("field6text")) {adddef($getcam->Fields("6ftype"), $getcam->Fields("field6text"),$getcam->Fields("id"), 6 );}
   if ($getcam->Fields("field7text")) {adddef($getcam->Fields("7ftype"), $getcam->Fields("field7text"),$getcam->Fields("id"), 7 );}
   if ($getcam->Fields("field8text")) {adddef($getcam->Fields("8ftype"), $getcam->Fields("field8text"),$getcam->Fields("id"), 8 );}
   if ($getcam->Fields("field9text")) {adddef($getcam->Fields("9ftype"), $getcam->Fields("field9text"),$getcam->Fields("id"), 9 );}
   if ($getcam->Fields("field10text")) {adddef($getcam->Fields("10ftype"), $getcam->Fields("field10text"),$getcam->Fields("id"), 10 );}
   
   $getcam->MoveNext(); }
 
 $getact=$dbcon->Execute("SELECT * from action") or DIE($dbcon->ErrorMsg());
  while  (!$getact->EOF) { 

$fieldid = getfid($getact->Fields("camid"), 1);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field1"));}
$fieldid = getfid($getact->Fields("camid"), 2);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field2"));}
$fieldid = getfid($getact->Fields("camid"), 3);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field3"));}
$fieldid = getfid($getact->Fields("camid"), 4);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field4"));}
$fieldid = getfid($getact->Fields("camid"), 5);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field5"));}
$fieldid = getfid($getact->Fields("camid"), 6);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field6"));}
$fieldid = getfid($getact->Fields("camid"), 7);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field7"));}
$fieldid = getfid($getact->Fields("camid"), 8);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field8"));}
$fieldid = getfid($getact->Fields("camid"), 9);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field9"));}
$fieldid = getfid($getact->Fields("camid"), 10);
if ($fieldid){  addv($getact->Fields("perid"),$fieldid,$getact->Fields("field10"));}
  
     $getact->MoveNext(); }
	 echo "done";
 ?>
