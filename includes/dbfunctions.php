<?php


###########################################
###Functions to insert, update and delte items from teh database
###########################################

// *** Update Record: construct a sql update statement and execute it
function update_record($MM_editTable,$MM_recordId,$MM_fieldsStr,$MM_columnsStr,$MM_editRedirectUrl=null,$MM_editColumn ="id"){
	global $MM_sysvar_mq, $dbcon;
	set_time_limit(0); 
	
	$MM_fields = Explode("|", $MM_fieldsStr);
	$MM_columns = Explode("|", $MM_columnsStr);

	// set the form values
	for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
		$MM_fields[$i+1] = $GLOBALS[$MM_fields[$i]];
	}

	// create the sql update statement
	$MM_editQuery = "update " . $MM_editTable . " set ";
	for ( $i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
	
		$formVal = $MM_fields[$i+1];
		$MM_typesArray = Explode(",", $MM_columns[$i+1]);
		$delim =    ($MM_typesArray[0] != "none") ? $MM_typesArray[0] : "";
		$altVal =   ($MM_typesArray[1] != "none") ? $MM_typesArray[1] : "";
		$emptyVal = ($MM_typesArray[2] != "none") ? $MM_typesArray[2] : "";
	
		if ($formVal == "" || !isset($formVal)) {
			$formVal = $emptyVal;
		} else {
	
			if ($altVal != "") {
				$formVal = $altVal;
			} elseif ($delim == "'") { 
				//deal with magic qoutes
	   			if ($MM_sysvar_mq != ("1")) {
					$formVal = "'" . str_replace("'","\'",$formVal) . "'";
				} else {
					$formVal = "'" .$formVal . "'";
				}
			} else {
				//done with magic quotes 
				$formVal = $delim . $formVal . $delim;
			}
	
		}
	
		if ($i != 0) {
			$MM_editQuery = $MM_editQuery . ", " . $MM_columns[$i] . " = " . $formVal;
		} else {
			$MM_editQuery = $MM_editQuery . $MM_columns[$i] . " = " . $formVal;
		}
	}
	
	$MM_editQuery = $MM_editQuery . " where " . $MM_editColumn . " = " . $MM_recordId;
	
	$queryrs = $dbcon->Execute($MM_editQuery) or DIE($dbcon->ErrorMsg());
	
	if ($MM_editRedirectUrl) {
			header ("Location: $MM_editRedirectUrl");
	}		 
	return $MM_recordId;
}

// *** Delete Record: construct a sql delete statement and execute it
function delete_record($MM_editTable,$MM_recordId,$MM_editRedirectUrl=null,$MM_editColumn="id") {
	global $dbcon;
	
	$MM_editQuery = "delete from " . $MM_editTable . " where " . $MM_editColumn . " = " . $MM_recordId;

	$queryrs = $dbcon->Execute($MM_editQuery) or DIE($dbcon->ErrorMsg());
	if ($MM_editRedirectUrl) {
		header ("Location: $MM_editRedirectUrl");
	}		 
	
}
  
// *** Insert Record: construct a sql insert statement and execute it
function insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr, $MM_editRedirectUrl=NULL) {
	global $MM_sysvar_mq, $dbcon;
	
	$MM_fields = Explode("|", $MM_fieldsStr);
	$MM_columns = Explode("|", $MM_columnsStr);
    
// set the form values
	for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
		$MM_fields[$i+1] = $GLOBALS[$MM_fields[$i]];
		//echo $MM_fields[$i+1];
	}

	// create the sql insert statement
	$MM_tableValues = "";
	$MM_dbValues = "";
	
	for ( $i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
	
		$formVal = $MM_fields[$i+1];
		$MM_typesArray = explode(",", $MM_columns[$i+1]);
	
		$delim = $MM_typesArray[0];
		if ($delim=="none") $delim="";
	
		$altVal = $MM_typesArray[1];
		if ($altVal=="none") $altVal="";
	
		$emptyVal = $MM_typesArray[2];
		if($emptyVal=="none") $emptyVal="";
	
		if ($formVal == "" || !isset($formVal)) {
			$formVal = $emptyVal;
		} else {
	
			if ($altVal != "") {
	
				$formVal = $altVal;
	
			} elseif ($delim == "'") { 
	
		       		//deal with magic qoutes
	 			if ($MM_sysvar_mq != ("1")) {
					$formVal = "'" . str_replace("'","\'",$formVal) . "'";
				} else {
					$formVal = "'" .$formVal . "'";
				}
				//done with magic quotes
			} else {
	
			        $formVal = $delim . $formVal . $delim;
			}
		}
	
		if ($i == 0) {
			$MM_tableValues = $MM_tableValues . $MM_columns[$i];
			$MM_dbValues = $MM_dbValues . $formVal;
		} else {
			$MM_tableValues = $MM_tableValues . "," . $MM_columns[$i];
			$MM_dbValues = $MM_dbValues . "," . $formVal;
		}
	}
	
	$MM_editQuery = "insert into " . $MM_editTable . " (" . $MM_tableValues . ") values (" . $MM_dbValues . ")";
	$db = $dbcon->Execute($MM_editQuery) or DIE("insert".$MM_editQuery.$dbcon->ErrorMsg());
	if ($MM_editRedirectUrl) {
		adpredirect($MM_editRedirectUrl);		
	}
	$lastid = $dbcon->Insert_Id();
	return $lastid;
}

function databaseactions() {
	global $_POST, $MM_editTable, $MM_fieldsStr, $MM_columnsStr, $MM_editRedirectUrl,$MM_editColumn,$MM_recordId;
	if ($_POST[MM_insert]) 	{
		$id =insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr, $MM_editRedirectUrl); 
	}
	if (($_POST[MM_update]) & ($_POST[MM_recordId])) {
		$id =update_record($MM_editTable,$MM_recordId,$MM_fieldsStr,$MM_columnsStr,$MM_editRedirectUrl);
	}
	if (($_POST[MM_delete]) & ($_POST[MM_recordId])) {
		delete_record($MM_editTable,$MM_recordId,$MM_editRedirectUrl);	
	}
	return $id;
}


###########################################
###Functions to add information into the contact system
# add uid to email and moduserdata
###########################################


function contactcheck($EmailAddress=NULL,$FirstName=NULL,$LastName=NULL) {
	global $dbcon;
	if ($EmailAddress) {
		$ce=$dbcon->Execute("Select id from contacts2 where FirstName = '$EmailAddress'    ") or DIE($dbcon->ErrorMsg());
		$id = $ce->Fields("id");
		if (!$id) {
			$cns=$dbcon->Execute("Select id from contacts2 where FirstName = '$FirstName' and LastName = '$LastName'   ") or DIE($dbcon->ErrorMsg());
			$id = $cns->Fields("id");

		}
	}
	if ((!$id) && ($FirstName) && ($LastName)) {
		$cn=$dbcon->Execute("Select id from contacts2 where FirstName = '$FirstName' and LastName = '$LastName'   ") or DIE($dbcon->ErrorMsg());
		$id = $cn->Fields("id");

	}
	return $id;
}

function contactinsert($source=1, $enteredby=2, $Organization=NULL, $FirstName=NULL, $LastName=NULL, $EmailAddress=NULL, $Phone=NULL, $Fax=NULL, $WebPage=NULL, $Address=NULL, $Address2=NULL, $City=NULL, $State=NULL, $PostalCode=NULL, $Country=NULL, $notes=NULL) {

	global $dbcon;
	$contactid = contactcheck($EmailAddress,$FirstName,$LastName);
	if (!$contactid) {
		$MM_editTable  = "contacts2";
		$source= $customfields->Fields("sourceid");
		$enteredby= $customfields->Fields("enteredby");
		$MM_fieldsStr = "Organization|value|FirstName|value|LastName|value|EmailAddress|value|Phone|value|Fax|value|WebPage|value|Address|value|Address2|value|City|value|State|value|PostalCode|value|Country|value|notes|value|source|value|enteredby|value";
		$MM_columnsStr = "Company|',none,''|FirstName|',none,''|LastName|',none,''|EmailAddress|',none,''|BusinessPhone|',none,''|BusinessFax|',none,''|WebPage|',none,''|BusinessStreet|',none,''|BusinessStreet2|',none,''|BusinessCity|',none,''|BusinessState|',none,''|BusinessPostalCode|',none,''|BusinessCountry|',none,''|notes|',none,''|source|none,none,NULL|enteredby|none,none,NULL";
		
		$contactid = insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr);
	}
	return $contactid;
}

function contactadduid($id,$uid,$table) {
	global $dbcon;
	$u=$dbcon->Execute("set $table uid ='$uid' where id = '$id'  ") or DIE($dbcon->ErrorMsg());
}

function addcontact($source=1, $enteredby=2, $Organization=NULL, $FirstName=NULL, $LastName=NULL, $EmailAddress=NULL, $Phone=NULL, $Fax=NULL, $WebPage=NULL, $Address=NULL, $Address2=NULL, $City=NULL, $State=NULL, $PostalCode=NULL, $Country=NULL, $notes=NULL) {
	global $dbcon;
	$uid= contactcheck($EmailAddress,$FirstName,$LastName);
	if (!$uid) {
		$uid= contactinsert($source, $enteredby, $Organization, $FirstName, $LastName, $EmailAddress, $Phone, $Fax, $WebPage, $Address, $Address2, $City, $State, $PostalCode, $Country, $notes);
	}
	
	return $uid;

}


###########################################
###PORT SCRIPTS TO PREPARE SYSTEM FOR CONTACTS ENTRY AND UDM STANDARIZATION e
###########################################

function getudmsource($modin) {
	global $dbcon;
	$m=$dbcon->Execute("select sourceid from modfields where id= $modinid") or DIE($dbcon->ErrorMsg());
	return $m->Fields("source");
	
}
function contactsfill() {
	global $dbcon;
	$uidsql = "where uid IS NULL or uid = ''";
	#tables to fill moduserdata, email, vol_person
	
	//DO UDM TABLES
	$t=$dbcon->Execute("Select id, EmailAddress, FirstName, LastName, modin from moduserdata $uidsql") or DIE($dbcon->ErrorMsg());
	while (!$t->EOF) {
		//check to see if there is a record
		$uid= contactcheck($t->Fields("EmailAddress"),$t->Fields("FirstName"),$t->Fields("LastName"));
		
		//if not get fields and insert record
		if (!$uid) {
			$g=$dbcon->Execute("Select * from moduserdata where id = ".$t->Fields("id")."") or DIE($dbcon->ErrorMsg());
			$source= getudmsource($g-> Fields("modinid"));
			
			$uid =contactinsert($source,2,$g->Fields("Organization"),$g->Fields("FirstName"),$g->Fields("LastName"),$g->Fields("EmailAddress"),$g->Fields("Phone"),$g->Fields("Fax"),$g->Fields("WebPage"),$g->Fields("Address"),$g->Fields("Address2"),$g->Fields("City"),$g->Fields("State"),$g->Fields("PostalCode"),$g->Fields("Country"),$g->Fields("notes"));
		}
		//update uid
		contactadduid($t->Fields("id"),$uid,'moduserdata');
		$t->MoveNext();
	}
	
	//DO EMAIL TABLE
	$t=$dbcon->Execute("Select id, email, firstname, lastname, modin from email $uidsql") or DIE($dbcon->ErrorMsg());
	while (!$t->EOF) {
		//check to see if there is a record
		$uid= contactcheck($t->Fields("emailaddress"),$t->Fields("firstname"),$t->Fields("lastname"));
		
		//if not get fields and insert record
		if (!$uid) {
			$g=$dbcon->Execute("Select * from email where id = ".$t->Fields("id")."") or DIE($dbcon->ErrorMsg());
			
			$uid =contactinsert(11,2,$g->Fields("organization"),$g->Fields("firstname"),$g->Fields("lastname"),$g->Fields("email"),$g->Fields("phone"),$g->Fields("fax"),$g->Fields("url"),$g->Fields("address1"),$g->Fields("address2"),$g->Fields("city"),$g->Fields("state"),$g->Fields("zip"),$g->Fields("country"),$g->Fields("description"));
		}
		//update uid
		contactadduid($t->Fields("id"),$uid,'email');
		$t->MoveNext();
	}

}


###########################################
###functions to port volunteer module to userdata module table
# create tables vol_availiblity and vol_relavailiblity
#
###########################################



function volavalsetup() {
	global $dbocn;
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('1','Monday Day')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('2','Monday Night')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('3','Tuesday Day')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('4','Tuesday Night')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('5','Wednesday Day')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('6','Wednesday Night')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('7','Thursday Day')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('8','Thursday Night')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('9','Friday Day')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('10','Friday Night')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('11','Saturday Day')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('12','Saturday Night')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('13','Sunday Day')") or DIE($dbcon->ErrorMsg());
	$v=$dbcon->Execute("insert in vol_availiblity (id,description) values ('14','Sunday Night')") or DIE($dbcon->ErrorMsg());
}

function volschin($id,$value) {
	$v=$dbcon->Execute("insert in vol_relavailiblity (personid,availibleid) values ('$id','$value')") or DIE($dbcon->ErrorMsg());

}

function volsch() {
	global $dbcon;
	$v=$dbcon->Execute("select * from vol_people where  ") or DIE($dbcon->ErrorMsg());
	while (!$v->EOF) {
		if ($v->Fields("mon_d")) {volschin($v->Fields("id"),1);}
		if ($v->Fields("mon_n")) {volschin($v->Fields("id"),2);}
		if ($v->Fields("tues_d")) {volschin($v->Fields("id"),3);}
		if ($v->Fields("tues_n")) {volschin($v->Fields("id"),4);}
		if ($v->Fields("wen_d")) {volschin($v->Fields("id"),5);}
		if ($v->Fields("wen_n")) {volschin($v->Fields("id"),6);}
		if ($v->Fields("thur_d")) {volschin($v->Fields("id"),7);}
		if ($v->Fields("thur_n")) {volschin($v->Fields("id"),8);}
		if ($v->Fields("fri_d")) {volschin($v->Fields("id"),9);}
		if ($v->Fields("fri_n")) {volschin($v->Fields("id"),10);}
		if ($v->Fields("sat_d")) {volschin($v->Fields("id"),11);}
		if ($v->Fields("sat_n")) {volschin($v->Fields("id"),12);}
		if ($v->Fields("sun_d")) {volschin($v->Fields("id"),13);}
		if ($v->Fields("sun_n")) {volschin($v->Fields("id"),14);}
		$v->MoveNext() ;
	}
}

function voltoudmrelupdate($personid,$id,$table) {
	$u=$dbcon->Execute("Update $table set personid ='$id' where personid = '$personid' ") or DIE($dbcon->ErrorMsg());
}


function voltoudm() {
	global $dbcon;
	$v=$dbcon->Execute("select * from vol_people ") or DIE($dbcon->ErrorMsg());
	while (!$v->EOF) {
		$Last_Name = $p->Fileds("last_name");
		$First_Name = $p->Fileds("first_name");
		$Email = $p->Fileds("email");
		$City = $p->Fileds("city");
		$State = $p->Fileds("state");
		$Zip = $p->Fileds("zip");
		$region = $p->Fileds("hood");
		$Street = $p->Fileds("address");
		$Phone = $p->Fileds("phone");
		$Work_Phone = $p->Fileds("phone2");
		$Cell_Phone = $p->Fileds("phone3");
		$Company = $p->Fileds("organization");
		$Notes = $p->Fileds("notes");
		$field1 = $p->Fileds("precinct");
		$field2 = $p->Fileds("bounce");
		$field3 = $p->Fileds("officenotes");
		$field4 = $p->Fileds("avalibility");
		$field5 = $p->Fileds("otherinterest");
		$field6= $p->Fileds("com1");
		$field7= $p->Fileds("com2");
		$field8 = $p->Fileds("com3");
 
		$modinid = 8;
		$publish =1;
		$personid = $p->Fileds("id");
		
		$MM_editTable  = "moduserdata";
		$MM_fieldsStr = "Organization|value|FirstName|value|LastName|value|EmailAddress|value|Phone|value|Fax|value|WebPage|value|Address|value|Address2|value|City|value|State|value|PostalCode|value|Country|value|notes|value|field1|value|field2|value|field3|value|field4|value|field5|value|field6|value|field7|value|field8|value|field9|value|field10|value|modin|value|field11|value|field12|value|field13|value|field14|value|field15|value|field16|value|field17|value|field18|value|field19|value|field20|value|region|value|uniqueid|value|pemail|value";
		$MM_columnsStr = "Organization|',none,''|FirstName|',none,''|LastName|',none,''|EmailAddress|',none,''|Phone|',none,''|Fax|',none,''|WebPage|',none,''|Address|',none,''|Address2|',none,''|City|',none,''|State|',none,''|PostalCode|',none,''|Country|',none,''|notes|',none,''|field1|',none,''|field2|',none,''|field3|',none,''|field4|',none,''|field5|',none,''|field6|',none,''|field7|',none,''|field8|',none,''|field9|',none,''|field10|',none,''|modinid|none,none,NULL|field11|',none,''|field12|',none,''|field13|',none,''|field14|',none,''|field15|',none,''|field16|',none,''|field17|',none,''|field18|',none,''|field19|',none,''|field20|',none,''|region|',none,''|uniqueid|',none,''|pemail|',none,''";
		$id = insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr);
		voltoudmrelupdate($personid,$id,'vol_reltask');
		voltoudmrelupdate($personid,$id,'vol_relinterest');
		voltoudmrelupdate($personid,$id,'vol_relskill');
		voltoudmrelupdate($personid,$id,'vol_relavalibility');
		// $uid = addcontact(4,,$Organization, $FirstName, $LastName, $EmailAddress, $Phone, $Fax, $WebPage, $Address, $Address2, $City, $State, $PostalCode, $Country, $notes);
		contactadduid($id,$uid,'moduserdata');
		$v->MoveNext(); 
	}

}

function setupvoludm() {
	global $dbcon;
	$field1text = "Precinct";//"Evening Phone";
	$field2text = "Email Bounce";//"Cell Phone";
	$field3text = "Office Notes";
	$field4text = "Other Availability";
	$field5text = "Specifically I would like to";
	$field6text = "Committee 1";
	$field7text = "Committee 2";
	$field8text = "Committee 3";
	$_1ftype = "1";
	$_2ftype ="2";
	$_3ftype ="3";
	$_4ftype ="3";
	$_5ftype ="3";
	$_6ftype ="1";
	$_7ftype ="1";
	$_8ftype ="1";

	$_1pub ="0";
	$_2pub ="0";
	$_3pub ="0";
	$_4pub ="1";
	$_5pub ="1";
	$_6pub ="1";
	$_7pub ="1";
	$_8pub ="0";

	$modidinput = "59";
	$modidresponse = "60";
	$subject = "new volunteer";
	$sourceid = 4;
	$enteredby = 2;
	$redirect = "modinput2.php?modin=8&thank=1";
	
	$MM_fieldsStr ="field1text|value|field2text|value|field3text|value|field4text|value|field5text|value|field6text|value|field7text|value|field8text|value|field9text|value|field10text|value|_1ftype|value|_2ftype|value|_3ftype|value|_4ftype|value|_5ftype|value|_6ftype|value|_7ftype|value|_8ftype|value|_9ftype|value|_10ftype|value|name|value|_1pub|value|_2pub|value|_3pub|value|_4pub|value|_5pub|value|_6pub|value|_7pub|value|_8pub|value|_9pub|value|_10pub|value|modidinput|value|modidresponse|value|sourceid|value|enteredby|value|subject|value|redirect|value";
	$MM_columnsStr = "field1text|',none,''|field2text|',none,''|field3text|',none,''|field4text|',none,''|field5text|',none,''|field6text|',none,''|field7text|',none,''|field8text|',none,''|field9text|',none,''|field10text|',none,''|1ftype|',none,''|2ftype|',none,''|3ftype|',none,''|4ftype|',none,''|5ftype|',none,''|6ftype|',none,''|7ftype|',none,''|8ftype|',none,''|9ftype|',none,''|10ftype|',none,''|name|',none,''|1pub|none,none,NULL|2pub|none,none,NULL|3pub|none,none,NULL|4pub|none,none,NULL|5pub|none,none,NULL|6pub|none,none,NULL|7pub|none,none,NULL|8pub|none,none,NULL|9pub|none,none,NULL|10pub|none,none,NULL|modidinput|none,none,NULL|modidresponse|none,none,NULL|sourceid|none,none,NULL|enteredby|none,none,NULL|subject|',none,''|redirect|',none,''";
	update_record('modfields',8,$MM_fieldsStr,$MM_columnsStr);

}

function volport() {
	setupvoludm();
	volavalsetup();
	volsch();
	voltoudm();
}

class dia {

	var $org_id;
	var $api_url;

	function dia ( $org_id, $url = null ) {

		// Set the access code / organizational ID.
		$this->org_id = $org_id;

		// Set a default api URL in the absence of one from the creator.
		$this->api_url = ( $url ) ? $url : "http://www.demaction.org/dia/api/process.jsp";

	}

	function add_supporter ( $email, $info = null ) {

		$info[ 'Email' ] = $email;

		return $this->process( "supporter", $info );

	}

	function process ( $table, $data ) {

		foreach ( $data as $key => $val ) {
			$req_str .= "&" . urlencode($key) . "=" . urlencode($val);
		}

		$req_url = $this->api_url . "?org=" . $this->org_id  . "&table=" . $table  . $req_str;

		$req = fopen( $req_url, "rb" );

		while (!feof($req)) {
			$out .= fread($req, 8192);
		}

		return $out;
	}

}

######################################################
######
######################################################

?>