<?PHP
//  ------ create table variable ------
// variables for Netscape Navigator 3 & 4 are +4 for compensation of render errors
$Browser_Type  =  strtok($HTTP_USER_AGENT,  "/");
if ( ereg( "MSIE", $HTTP_USER_AGENT) || ereg( "Mozilla/5.0", $HTTP_USER_AGENT) || ereg ("Opera/5.11", $HTTP_USER_AGENT) ) {
	$theTable = 'WIDTH="400" HEIGHT="245"';
} else {
	$theTable = 'WIDTH="404" HEIGHT="249"';
}

// ------ create document-location variable ------
if ( ereg("php\.exe", $PHP_SELF) || ereg("php3\.cgi", $PHP_SELF) || ereg("phpts\.exe", $PHP_SELF) ) {
	// $documentLocation = $HTTP_ENV_VARS["PATH_INFO"];
	$documentLocation = getenv("PATH_INFO");
} else {
	$documentLocation = $PHP_SELF;
}
if ( getenv("QUERY_STRING") ) {
	$documentLocation .= "?" . getenv("QUERY_STRING");
}

?>
<html><head>
	<title><?PHP echo $strLoginInterface; ?></title>
	
<SCRIPT LANGUAGE="JavaScript">
<!--
//  ------ check form ------
function checkData() {
	var f1 = document.forms[0];
	var wm = "<?PHP echo $strJSHello; ?>\n\r\n";
	var noerror = 1;

	// --- entered_login ---
	var t1 = f1.entered_login;
	if (t1.value == "" || t1.value == " ") {
		wm += "<?PHP echo $strLogin; ?>\r\n";
		noerror = 0;
	}

	// --- entered_password ---
	var t1 = f1.entered_password;
	if (t1.value == "" || t1.value == " ") {
		wm += "<?PHP echo $strPassword; ?>\r\n";
		noerror = 0;
	}

	// --- check if errors occurred ---
	if (noerror == 0) {
		alert(wm);
		return false;
	}
	else return true;
}
//-->
</SCRIPT>

<style type="text/css">
<!-- 
A:hover.link {
	background-color: #E9E9E9;
}
//-->
</style>
</head>

<body bgcolor="White" TEXT="Black"><center>
<form action='<?PHP echo $documentLocation; ?>' METHOD="post" onSubmit="return checkData()">
<TABLE WIDTH="100%" HEIGHT="100%" CELLPADDING="0" CELLSPACING="0"><TR>
        <TD ALIGN="center" VALIGN="middle"> 
          
          <p><strong><font color="#006699" size="5" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $SiteName ?>
            <br>
            Administration</font></strong></p>
            <?PHP
			// check for error messages
			if ($message) {
				echo $message;
			} ?>
          <table cellpadding=4 cellspacing=1 BACKGROUND="">
            <tr> 
              <td bgcolor="#FFFFFF"><B><FONT color="#000000" SIZE="-1" FACE="Arial,Helvetica,sans-serif">Login: 
                </FONT></B></td>
              <td> <INPUT TYPE="text" NAME="entered_login" STYLE="font-size: 9pt;" TABINDEX="1"></td>
            </tr>
            <tr> 
              <td bgcolor="#FFFFFF"><B><FONT color="#000000" SIZE="-1" FACE="Arial,Helvetica,sans-serif">Password: 
                </FONT></B></td>
              <td> <INPUT TYPE="password" NAME="entered_password" STYLE="font-size: 9pt;" TABINDEX="1"></td>
            </tr>
          </table>
          <INPUT name="submit" TYPE=submit value="Login">
          <br>
          <br> 

          <font size="2" face="Verdana, Arial, Helvetica, sans-serif">If you are 
          having trouble logging in, please contact the <a href="mailto:<?php  echo $admEmail; ?>">site 
          administrator</a></font></TD>
      </TR></TABLE>
</form>
</center>

<SCRIPT LANGUAGE="JavaScript">
<!--
document.forms[0].entered_login.select();
document.forms[0].entered_login.focus();
//-->
</SCRIPT>
</body></html>
