<?PHP 
$modid = 22;
if ($submit) {$mod_id = 32 ; }
else {$mod_id = 33;};

include("sysfiles.php");
include("header.php");


   
$messagetext="";

error_reporting(0);
# Path to mailto.php script:
	$GLOBALS["path"]="mailto2.php";
# Site name:
	$GLOBALS["site_name"]=  $SiteName;
# webmaster's email:
	//$GLOBALS["your_email"]= $setvar->Fields("emfrom");
	



############################ DO NOT EDIT BELOW ################################
function show_form() {
global $messsageor;
?>

<table border="0" cellspacing="0" cellpadding="0" align=center width="90%"  height="100%" >
<tr>
      
    <td rowspan="1" colspan="1" valign="Top" align="Left" >
        <script language="JavaScript">
<!-- 
function checkForm( theForm ) {
	if ((theForm.email.value == "") ||
		(theForm.firstname.value == "") ||
		(theForm.lastname.value == "") ||
		(theForm.friend1.value == "")) {
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
	if ((theForm.friend9.value != "") && (!isEmail(theForm.friend9.value))) {
		alert("The email address that was entered is not valid. Please revise.");
		theForm.friend9.select();
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
      </p>
      <form name="emailForm" action="mailto2.php" METHOD=POST ONSUBMIT="return checkForm(this);">
       <p> <font color="#FF0000">required fields are red</font></p>
       
        <TABLE border="0" align="center" cellpadding="3" cellspacing="" bordercolorlight="" bordercolordark="" bgcolor="" class="text">
          <TR> 
            <TD align="left" valign="top"><B>From: </B> </TD>
            <TD align="left" valign="middle"><font color="#FF0000">First:</font></TD>
            <TD colspan="1" align="left"><input name="firstname" type="text" id="firstname" value="" size="30" maxlength="50"> 
            </TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left"><font color="#FF0000">Last:</font></TD>
            <TD colspan="1" align="left"><input name="lastname" type="text" id="lastname" value="" size="30" maxlength="50"></TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left"><font color="#FF0000">E-Mail:</font></TD>
            <TD colspan="1" align="left"><input name="email" type="text" id="email" value="" size="30" maxlength="50"></TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left">&nbsp;</TD>
            <TD colspan="1" align="left">&nbsp;</TD>
          </TR>
          <TR> 
            <TD align="left" valign="top"><strong>To:</strong></TD>
            <TD align="left"><font color="#FF0000">Email:</font></TD>
            <TD colspan="1" align="left"><input name="friend1" type="text" id="friend1" value="" size="30" maxlength="50"></TD>
          </TR>
          <TR> 
            <TD height="24" align="left">&nbsp;</TD>
            <TD align="left">Email:</TD>
            <TD colspan="1" align="left"><input maxlength="50" name="friend2" size="30" type="text" value=""></TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left">Email:</TD>
            <TD colspan="1" align="left"><input maxlength="50" name="friend3" size="30" type="text" value=""></TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left">Email:</TD>
            <TD colspan="1" align="left"><input maxlength="50" name="friend4" size="30" type="text" value=""></TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left">Email:</TD>
            <TD colspan="1" align="left"><input maxlength="50" name="friend5" size="30" type="text" value=""></TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left">Email:</TD>
            <TD colspan="1" align="left"><input maxlength="50" name="friend6" size="30" type="text" value=""></TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left">Email:</TD>
            <TD colspan="1" align="left"><input maxlength="50" name="friend7" size="30" type="text" value=""></TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left">Email:</TD>
            <TD colspan="1" align="left"><input maxlength="50" name="friend8" size="30" type="text" value=""></TD>
          </TR>
          <TR> 
            <TD align="left">&nbsp;</TD>
            <TD align="left">Email:</TD>
            <TD colspan="1" align="left"><input maxlength="50" name="friend9" size="30" type="text" value=""></TD>
          </TR>
          <TR> 
            <TD colspan="3" align="left"><strong>Your message (optional)</strong></TD>
          </TR>
          <TR> 
            <TD colspan="3" align="left"><textarea name="usermessage" cols="45" rows="20" wrap="VIRTUAL" id="usermessage"><?php echo $messsageor; ?></textarea></TD>
          </TR>
          <TR> 
            <TD colspan="3"><input name="submit" type="submit" value="Send"></TD>
          </TR>
          <TR> 
            <TD colspan="3">&nbsp;</TD>
          </TR>
        </TABLE>
 <input type=hidden name=object_id value="2919">
  <input type=hidden name=story_url value="">
 </FORM>
</td></tr>
</table>


<?PHP
}

function sendemail($who) {
global $firstname,$lastname,$email,$usermessage,$messagetext,$subject, $GLOBALS; 	
   if (isset($who)) {
	$date=date( "D, j M Y H:i:s -0600");
	$from= $firstname." ".$lastname;
	$from_email=$email;
#$usermessage;
 
	
	$message="$from asked that this be sent to you.\n";
		if ($usermessage != "") {
			$message.="Note:\n$usermessage\n";
		}
		$message.=$messagetext;
	
	$add="From: $from_email <$from_email>\nReply-To: $from_email\nDate: $date\n";
	mail ("$who","$subject","$message","$add");
	}
	
	}// end function


if ($submit) {
sendemail($friend1);
sendemail($friend2);
sendemail($friend3);
sendemail($friend4);
sendemail($friend5);
sendemail($friend6);
sendemail($friend7);
sendemail($friend8);
sendemail($friend9);


}
else {
show_form();
}


?>
<?php include("footer.php"); ?>