<?php
# functions to subscribe an email system

function e_emailcheck($email) {
	global $dbcon;
	$getemails=$dbcon->Execute("select id from email where email = '$email'") or DIE("6".$dbcon->ErrorMsg());
	if  ($getemails->Fields("id")){ 
		$emailid = $getemails->Fields("id");
		return $emailid;
	}
}

function e_subcheck($emailid, $listid) {
	global $dbcon;
	$sql= "SELECT id FROM  subscription where userid =$emailid and listid=$listid";
	$getemails=$dbcon->Execute($sql) or DIE("16".$sql.$dbcon->ErrorMsg());
	if  ($getemails->Fields("id")){
		return  true;
	}
}

function e_subadd($emailid, $listid) {
	global $dbcon;
	$emailid = $emailid;
	$listid= $listid;
	$sql= "insert into subscription (userid,listid) values ($emailid,$listid)";
	$in= $dbcon->Execute($sql) or DIE("sql".$dbcon->ErrorMsg());
}

function e_emailadd($email) {
	global $dbcon, $_POST;
	global  $First_Name, $Last_Name,  $Company, $Street, $Street_2, $City, $State, $Zip, $Country, $Web_Page, $Phone, $Fax;
	$MM_editTable  = "email";
	$MM_fieldsStr = "Email|value|Last_Name|value|First_Name|value|Company|value |html|value|Phone|value|Web_Page|value|Street|value|Street_|value|City|value|State|value|Zip|value|Country|value|Fax|value";
	$MM_columnsStr = "email|',none,''|lastname|',none,''|firstname|',none,''|organization|',none,''|html|none,1,0|phone|',none,''|url|',none,''|address1|',none,''|address2|',none,''|city|',none,''|state|',none,''|zip|',none,''|country|',none,''|fax|',none,''";
	insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr); 
	$emailid = e_getid($email);
	return $emailid;
}
 	
function e_getid($email){
	global $dbcon;
	$getemails=$dbcon->Execute("select id from email where email = '$email'") or DIE("42".$dbcon->ErrorMsg());
	$emailid = $getemails->Fields("id");
	return $emailid;
}

function e_addemail($email, $listid) {
	if (email_is_valid($email)){
		$emailid = e_emailcheck($email);
		if ($emailid) {  //echo $emailid;
			if (e_subcheck($emailid, $listid) != true) {e_subadd($emailid, $listid);
			}
		}
		else {
 			$emailid = e_emailadd($email);
			e_subadd($emailid, $listid);
		}
	}
}
############################################################

function emailcheck($email) {
	global $dbcon;
	$getemails=$dbcon->Execute("select id from phplist_user_user where email = '$email'") or DIE($dbcon->ErrorMsg());
	if  ($getemails->Fields("id")){ 
		$emailid = $getemails->Fields("id");
		return $emailid;
	}
}

function subcheck($emailid, $listid) {
	global $dbcon;
	$getemails=$dbcon->Execute("select userid from  phplist_listuser where userid = $emailid and listid = $listid") or DIE($dbcon->ErrorMsg());
	if  ($getemails->Fields("userid")){
		return  true;
	}
}

function subadd($emailid, $listid) {
	global $dbcon;
	$MM_editTable  = " phplist_listuser";
 	$MM_fieldsStr = "emailid|value|listid|value|entered|value";
  	$MM_columnsStr = "userid|',none,''|listid|',none,''|entered|',none,now()";
	insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr); 
}

function emailadd($email) {
	global $dbcon;
	mt_srand((double)microtime()*1000000);
	$randval = mt_rand();
	$MM_editTable  = "phplist_user_user";
	$MM_fieldsStr = "email|value|confirmed|value|randval|value|htmlemail|value|entered|value";
	$MM_columnsStr = "email|',none,''|confirmed|none,1,1|uniqid|',none,''|htmlemail|none,1,1|entered|',none,now()";
	insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr); 
	$emailid = getid($email);
	return $emailid;
}

function getstate($state) {
	global $dbcon;
 	$getstate=$dbcon->Execute("select state from states where id = $state ") or DIE($dbcon->ErrorMsg());
	$state= $getstate->Fields("state");
	return $state;
}

function getid($email){
	global $dbcon;
	$getemails=$dbcon->Execute("select id from phplist_user_user where email = '$email'") or DIE($dbcon->ErrorMsg());
	$emailid = $getemails->Fields("id");
	return $emailid;
}

function  emailprop($emailid) {
	global  $First_Name, $Last_Name,  $Organization, $Address, $Address2, $City, $State, $PostalCode,  $Country, $WebPage, $Phone, $Fax;
	if ($FirstName) {emailat(1,$FirstName,$emailid);}
	if ($LastName) {emailat(12,$LastName,$emailid);}
	if ($Organization) {emailat(19,$Organization,$emailid);}
	if ($Address) {emailat(13,$Address,$emailid);}
	if ($Address2) {emailat(26,$Address2,$emailid);}
	if ($City) {emailat(14,$City,$emailid);}
	if ($PostalCode) {emailat(20,$PostalCode,$emailid);}
	if ($Country) {emailat(2,$Country,$emailid);}
	if ($Phone) {emailat(25,$Phone,$emailid);}
	if ($Fax) {emailat(24,$Fax,$emailid);}
	if ($WebPage) {emailat(27,$WebPage,$emailid);}
	if ($State) {
		$State= getstate($State);
		emailat(22,$State,$emailid);
	}
}

function emailat($id,$valuex,$recid) {
	global $dbcon;
	$attributeid = $id;
	$MM_editTable  = "phplist_user_user_attribute";
 	$MM_fieldsStr = "recid|value|attributeid|value|valuex|value";
  	$MM_columnsStr = "userid|',none,''|attributeid|',none,''|value|',none,''";
	insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr); 
}
						
function addemail($email, $listid) {
	if (email_is_valid($email)){
		$emailid = emailcheck($email);
		if ($emailid) {  echo $emailid;
			if (subcheck($emailid, $listid) != true) {subadd($emailid, $listid);
			}
		}
		else {
 			$emailid = emailadd($email);
 			emailprop($emailid);
			subadd($emailid, $listid);
		}
	}		
}
	
function tellfriend($firstname,$lastname,$email,$subject,$text) {
?>
  <script language="JavaScript">
<!-- 
function checkForm( theForm ) {
	if ((theForm.FromEmail.value == "") ||
		(theForm.name.value == "") ||
		(theForm.subject.value == "") ||
		(theForm.friend1.value == "") ||
		(theForm.text.value == "")) {
			alert("All fields must be filled out before the email can be sent.");
			return false;
	}
	
	if (!isEmail(theForm.email.value)){
		alert("The email address that was entered is not valid. Please revise.");
		theForm.email.select();
		return false;
	}
	
	if (!isEmail(theForm.friend1.value)){
		alert("The email address that was entered is not valid. Please revise.");
		theForm.friend1.select();
		return false;
	}
	
	if ((theForm.friend2.value != "") && (!isEmail(theForm.friend2.value))) {
		alert("The email address that was entered is not valid. Please revise.");
		theForm.friend2.select();
		return false;
	}
	
	if ((theForm.friend3.value != "") && (!isEmail(theForm.friend3.value))) {
		alert("The email address that was entered is not valid. Please revise.");
		theForm.friend3.select();
		return false;
	}
	
	if ((theForm.friend4.value != "") && (!isEmail(theForm.friend4.value))) {
		alert("The email address that was entered is not valid. Please revise.");
		theForm.friend4.select();
		return false;
	}
	
	if ((theForm.friend5.value != "") && (!isEmail(theForm.friend5.value))) {
		alert("The email address that was entered is not valid. Please revise.");
		theForm.friend5.select();
		return false;
	}
		if ((theForm.friend6.value != "") && (!isEmail(theForm.friend6.value))) {
		alert("The email address that was entered is not valid. Please revise.");
		theForm.friend6.select();
		return false;
	}
	if ((theForm.friend7.value != "") && (!isEmail(theForm.friend7.value))) {
		alert("The email address that was entered is not valid. Please revise.");
		theForm.friend7.select();
		return false;
	}
	if ((theForm.friend8.value != "") && (!isEmail(theForm.friend8.value))) {
		alert("The email address that was entered is not valid. Please revise.");
		theForm.friend8.select();
		return false;
	}


	
	return true;
}

function isEmail(email) {
	// email at least 5 characters long
	if (email.length < 5)
		return false;
	// email has an @ as at least the second character
	else if (email.indexOf("@") < 1)
		return false;
	// @ and . separated by at least 1 character
	else if ((email.indexOf(".", email.indexOf("@")) - email.indexOf("@")) < 2)
		return false;

	// email does not contain any illegal characters
	// "\" is escape character, thus the double "\\"
	var illegals = "()<>,;:\\[]*/";
	for (i=0; i < illegals.length; i++) {
		if (email.indexOf(illegals.charAt(i)) != -1)
			return false;
	}	
	
	return true;
}
// -->
</script>
 <form name="emailForm" action="mailto2.php" METHOD=POST ONSUBMIT="return checkForm(this);">
  <table width="450" cellpadding="0" border="0" cellspacing="0">
    <tr> 
      <td colspan="3"> <font size="-1">*required fields</font><br> <br> </td>
    </tr>
    <tr> 
      <td colspan="3" class="form">From:</td>
    </tr>
    <tr> 
      <td width="100" align="right">* Name:&nbsp;</td>
      <td colspan="2" width="350" height="25"> <input name="name" type=text class="formField" id="name" style="width: 250px;" value="<?php echo $firstname."&nbsp;".$lastname; ?>" size="25"> 
      </td>
    </tr>
    <tr> 
      <td width="100" align="right">*Email:&nbsp;</td>
      <td colspan="2" width="350" height="25"> <input type=text name="FromEmail" value="<?php echo $email; ?>" size="25" style="width: 250px;" class="formField"> 
      </td>
    </tr>
    <tr> 
      <td width="100" align="right">*Subject:&nbsp;</td>
      <td colspan="2" width="350" height="25"> <input name="subject" type=text class="formField" id="subject" style="width: 250px;" value="<?php echo $subject; ?>" size="25"> 
      </td>
    </tr>
    <tr> 
      <td colspan="3" class="form"><br>
        To:</td>
    </tr>
    <tr> 
      <td width="100" align="right">*Email:</td>
      <td width="350" height="25"> <input name="friend1" type="text" class="formField" id="friend1" style="width: 145px;" value="" size="14"> 
      </td>
      <td width="190" height="25"> <input name="friend5" type="text" class="formField" id="friend5" style="width: 145px;" value="" size="14"> 
      </td>
    </tr>
    <tr> 
      <td width="100" align="right">Email:</td>
      <td width="350" height="25"> <input name="friend2" type="text" class="formField" id="friend2" style="width: 145px;" value="" size="14"> 
      </td>
      <td width="190" height="25"> <input name="friend6" type="text" class="formField" id="friend6" style="width: 145px;" value="" size="14"> 
      </td>
    </tr>
    <tr> 
      <td width="100" align="right">Email:</td>
      <td width="350" height="25"> <input name="friend3" type="text" class="formField" id="friend3" style="width: 145px;" value="" size="14"> 
      </td>
      <td width="190" height="25"> <input name="friend7" type="text" class="formField" id="friend7" style="width: 145px;" value="" size="14"> 
      </td>
    </tr>
    <tr> 
      <td width="100" align="right">Email:</td>
      <td width="350" height="25"> <input name="friend4" type="text" class="formField" id="friend4" style="width: 145px;" value="" size="14"> 
      </td>
      <td width="190" height="25"> <input name="friend8" type="text" class="formField" id="friend8" style="width: 145px;" value="" size="14"> 
      </td>
    </tr>
    <tr> 
      <td colspan="3" class="form"><br> <br>
        *Message:</td>
    </tr>
    <tr> 
      <td width="100">&nbsp;</td>
      <td valign="top" colspan="2"> <textarea rows="10" cols="28" wrap="soft" style="width: 250px;" name="text" ><?php echo $text; ?></textarea> 
      </td>
    </tr>
    <tr> 
      <td width="100">&nbsp;</td>
      <td colspan="2" width="350"> <br> </td>
    </tr>
    <tr> 
      <td width="100">&nbsp;</td>
      <td colspan="2" width="350"> <input type="Submit" value="Tell Your Friends"> 
        <br> <br> </td>
    </tr>
  </table>
</form>
<?php
}
		
function friendsend($email,$fromemail,$fromname,$subject,$text) {
	$headers="From: $fromname <$fromemail>\nReply-To: $fromemail";
	if (emailisvalid($email)) {
		mail ($email,$subject,$text,$headers);
	}
}			
			
function friendthankyou() {
	echo "<br><br><b>Thank You for Spreading the Word</b>";
}
						
?>