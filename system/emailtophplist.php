<?php
# create lists
# move users
# move attributes
# move subscriptions
#


set_time_limit(0);

 require("Connections/freedomrising.php");


function tranferlists() {
global $dbcon;
$empty=$dbcon->Execute("delete from phplist_list") or DIE($dbcon->ErrorMsg());
$sql= "select * from lists ";
$list=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());
while (!$list->EOF) {
$id = $list->Fields("id");
$name  = $list->Fields("name");
$description = $list->Fields("description");
$active = $list->Fields("publish");
$owner = 1;
$listorder = 0;

$MM_insert=1;
$MM_editTable  = "phplist_list";
$MM_fieldsStr = "id|value|name|value|description|value|active|value|owner|value|listorder|value|entered|value";
$MM_columnsStr = "id|',none,''|name|',none,''|description|',none,''|active|',none,''|owner|',none,''|listorder|',none,''|entered|',none,NOW()";
 							    require ("../Connections/insetstuff.php");
  								require ("../Connections/dataactions.php");
	$list->MoveNext();  } 
}

function emailat($id,$valuex,$recid) {
					global $dbcon;
					   $attributeid = $id;
					   $MM_insert=1;
					   $MM_editTable  = "phplist_user_user_attribute";
 					   $MM_fieldsStr = "recid|value|attributeid|value|valuex|value";
  					   $MM_columnsStr = "userid|',none,''|attributeid|',none,''|value|',none,''";
 							    require ("../Connections/insetstuff.php");
  								require ("../Connections/dataactions.php");
						}			
						
function moveemail () {
global $dbcon;
$empty=$dbcon->Execute("delete from phplist_user_user") or DIE($dbcon->ErrorMsg());
$empty=$dbcon->Execute("delete from phplist_user_user_attribute") or DIE($dbcon->ErrorMsg());
$sql= "select * from email ";
$allemails=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());

while ((!$allemails->EOF)){
$recid =  $allemails->Fields("id");
$email = $allemails->Fields("email");
//move attributes
					if ($allemails->Fields("firstname")) {emailat(1,$allemails->Fields("firstname"),$recid);}
					if ($allemails->Fields("lastname")) {emailat(12,$allemails->Fields("lastname"),$recid);}
					if ($allemails->Fields("organization")) {emailat(19,$allemails->Fields("organization"),$recid);}
					if ($allemails->Fields("address1")) {emailat(13,$allemails->Fields("address1"),$recid);}
					if ($allemails->Fields("address2")) {emailat(26,$allemails->Fields("address2"),$recid);}
					if ($allemails->Fields("city")) {emailat(14,$allemails->Fields("city"),$recid);}
					if ($allemails->Fields("state")) {emailat(22,$allemails->Fields("state"),$recid);}
					if ($allemails->Fields("zip")) {emailat(20,$allemails->Fields("zip"),$recid);}
					if ($allemails->Fields("country")) {emailat(2,$allemails->Fields("country"),$recid);}
					if ($allemails->Fields("phone")) {emailat(25,$allemails->Fields("phone"),$recid);}
					if ($allemails->Fields("fax")) {emailat(24,$allemails->Fields("fax"),$recid);}
					if ($allemails->Fields("url")) {emailat(27,$allemails->Fields("url"),$recid);}
	 
	$MM_insert =1;
					  		
							$randval = md5(uniqid(mt_rand()));
					   $MM_editTable  = "phplist_user_user";
 					   $MM_fieldsStr = "recid|value|email|value|confirmed|value|randval|value|htmlemail|value|entered|value";
  					   $MM_columnsStr = "id|',none,''|email|',none,''|confirmed|none,1,1|uniqid|',none,''|htmlemail|none,1,1|entered|',none,now()";

		require ("../Connections/insetstuff.php");
    	require ("../Connections/dataactions.php"); 

$allemails->MoveNext();  } 
}


function movesubscriptions() {
global $dbcon;
$empty=$dbcon->Execute("delete from phplist_listuser") or DIE($dbcon->ErrorMsg());
$sql= "select * from subscription ";
$subsc=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());
while (!$subsc->EOF) {

$userid =$subsc->Fields("userid");
$listid=$subsc->Fields("listid");
$check=$dbcon->Execute("select * from phplist_listuser where userid = $userid and listid = $listid ") or DIE($dbcon->ErrorMsg());
if (!$check->Fields("userid")) {
$MM_insert =1;
	$MM_editTable  = "phplist_listuser";
 					   $MM_fieldsStr = "userid|value|listid|value|entered|value";
  					   $MM_columnsStr = "userid|',none,''|listid|',none,''|entered|',none,now()";
					   require ("../Connections/insetstuff.php");
    	require ("../Connections/dataactions.php"); 
		}
$subsc->MoveNext();  } 
}

tranferlists();
moveemail();
movesubscriptions();
echo "done";
?>