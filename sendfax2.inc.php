<?php

 $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  
	if (isset($MM_insert)){

 $MM_editTable  = "tookaction";
 
$MM_editRedirectUrl =  "http://www.congressmerge.com/onlinedb/cgi-bin//addrseek.cgi?site=$CM_site&bccok=on&bccemail=$CM_bccaddress&issuecode=$issuecode&name_prefix=$ame_prefix&name_first=$name_first&name_mi=$name_mi&name_last=$name_last&address=$address&city=$city&state=$state&zipcode=$zipcode&email=$email&phone=$phone&Submit=Review+Letter&name_prefix=$name_prefix";

   $MM_fieldsStr =  "name_first|value|name_last|value|address|value|city|value|zipcode|value|email|value|phone|value|actionid|value|state|value";
   $MM_columnsStr = "firstname|',none,''|lastname|',none,''|address|',none,''|city|',none,''|zip|',none,''|email|',none,''|phone|',none,''|actionid|',none,''|state|',none,''";


 require ("DBConnections/insetstuff.php");
  require ("DBConnections/dataactions.php");
  
   }

$item = $Recordset1->Fields("actionlink");

   $sendfax=$dbcon->Execute("SELECT *  FROM sendfax  WHERE id = $item and emailfax = 0") or DIE($dbcon->ErrorMsg());
 $issuecode =  $sendfax->Fields("issuecode");
?>
 



<form method="POST" action="<?php echo $MM_editAction?>" class="form">
  <TABLE BORDER=0 class="text"><TR>
      <TD COLSPAN=2 class="text"><hr>
        Enter your <I>home address</I> below and our database will automatically 
        determine who represents you in Congress. Where electronic access is possible, 
        you can send an e-mail directly to your Senators and Representative. In 
        cases where a member of Congress does not accept electronic communications, 
        you will be provided with their ground mail address. Currently, about 
        97% of members of Congress accept electronic communications of some sort. 
        <p><b><?php echo $sendfax->Fields("subject")?></b><br>
          <BR>
        </p>
        </TD></TR>      <TR> 
        <TD ALIGN=RIGHT VALIGN=TOP><font color="#000000">Your Name:</font></TD>
        <TD> 
	  <TABLE BORDER=0>
	  <TR> 
          <TD><SELECT NAME="name_prefix">
            <OPTION VALUE="Mr" SELECTED>Mr.
            <OPTION VALUE="Mrs">Mrs.
            <OPTION VALUE="Miss">Miss
            <OPTION VALUE="Ms">Ms.
            <OPTION VALUE="Dr">Dr.
          </SELECT></TD>
          <TD><INPUT TYPE=text NAME="name_first" VALUE="" SIZE=20 MAXLENGTH=20></TD>
          <TD ALIGN=CENTER><INPUT TYPE=text NAME="name_mi" VALUE="" SIZE=1 MAXLENGTH=1></TD>
          <TD><INPUT TYPE=text NAME="name_last" VALUE="" SIZE=30 MAXLENGTH=30></TD>
	  </TR>
	  <TR VALIGN=TOP>
          <TD>&nbsp;</TD>
            <TD class="text">(first name)</TD>
            <TD ALIGN=CENTER class="text">(middle initial)</TD>  <TD class="text">(last/family name)</TD>
	  </TR>
	  </TABLE>
        </TD>
      </TR>
      <TR> 
        <TD ALIGN=RIGHT><font color"#000000">Street Address:</FONT></TD>
        <TD> 
          <INPUT TYPE=text NAME="address" VALUE="" SIZE=30 MAXLENGTH=30>
        </TD>
      </TR>
      <TR> 
        <TD ALIGN=RIGHT><font color"#000000">City:</FONT></TD>
        <TD> 
          <INPUT TYPE=text NAME="city" VALUE="" SIZE=30 MAXLENGTH=30>
        </TD>
      </TR>
      <TR> 
        <TD ALIGN=RIGHT><font color"#000000">State:</font></TD>
        <TD> 
          <SELECT NAME="state">
            <OPTION VALUE="AL">Alabama 
            <OPTION VALUE="AK">Alaska 
            <OPTION VALUE="AS">American Samoa 
            <OPTION VALUE="AZ">Arizona 
            <OPTION VALUE="AR">Arkansas 
            <OPTION VALUE="CA">California 
            <OPTION VALUE="CO">Colorado 
            <OPTION VALUE="CT">Connecticutt 
            <OPTION VALUE="DE">Delaware 
            <OPTION VALUE="DC">District of Columbia 
            <OPTION VALUE="FL">Florida 
            <OPTION VALUE="GA">Georgia 
            <OPTION VALUE="GU">Guam 
            <OPTION VALUE="HI">Hawaii 
            <OPTION VALUE="ID">Idaho 
            <OPTION VALUE="IL">Illinois 
            <OPTION VALUE="IN">Indiana 
            <OPTION VALUE="IA">Iowa 
            <OPTION VALUE="KS">Kansas 
            <OPTION VALUE="KY">Kentucky 
            <OPTION VALUE="LA">Louisiana 
            <OPTION VALUE="ME">Maine 
            <OPTION VALUE="MD">Maryland 
            <OPTION VALUE="MA">Masschusetts 
            <OPTION VALUE="MI">Michigan 
            <OPTION VALUE="MN">Minnesota 
            <OPTION VALUE="MS">Mississippi 
            <OPTION VALUE="MO">Missouri 
            <OPTION VALUE="MT">Montana 
            <OPTION VALUE="NE">Nebraska 
            <OPTION VALUE="NV">Nevada 
            <OPTION VALUE="NH">New Hampshire 
            <OPTION VALUE="NJ">New Jersey 
            <OPTION VALUE="NM">New Mexico 
            <OPTION VALUE="NY">New York 
            <OPTION VALUE="NC">North Carolina 
            <OPTION VALUE="ND">North Dakota 
            <OPTION VALUE="OH">Ohio 
            <OPTION VALUE="OK">Oklahoma 
            <OPTION VALUE="OR">Oregon 
            <OPTION VALUE="PA">Pennsylvania 
            <OPTION VALUE="PR">Puerto Rico 
            <OPTION VALUE="RI">Rhode Island 
            <OPTION VALUE="SC">South Carolina 
            <OPTION VALUE="SD">South Dakota 
            <OPTION VALUE="TN">Tennesse 
            <OPTION VALUE="TX">Texas 
            <OPTION VALUE="UT">Utah 
            <OPTION VALUE="VT">Vermont 
            <OPTION VALUE="VI">Virgin Islands 
            <OPTION VALUE="VA">Virginia 
            <OPTION VALUE="WA">Washington 
            <OPTION VALUE="WV">West Virginia 
            <OPTION VALUE="WI">Wisconsin 
            <OPTION VALUE="WY">Wyoming 
          </SELECT>
        </TD>
      </TR>
      <TR> 
        <TD ALIGN=RIGHT><font color"#000000">Zip Code:</FONT></TD>
        <TD> 
          <INPUT TYPE=text NAME="zipcode" VALUE="" SIZE=5 MAXLENGTH=5>
        </TD>
      <TR> 
      <TR VALIGN="TOP"> 
        <TD ALIGN=RIGHT><font color"#000000">E-mail Address:</FONT></TD>
        <TD> 
          <INPUT TYPE=text NAME="email" VALUE="" SIZE=30>
          <BR>
          (This email address is used to send a copy of your message to you.)</TD>
      <TR> 
      <TR VALIGN="TOP"> 
        <TD ALIGN=RIGHT><font color"#000000">Phone Number: </FONT></TD>
        <TD> 
          <INPUT TYPE=text NAME="phone" VALUE="" SIZE=15 MAXLENGTH=15>
          <BR>
          <FONT color"#000000">(Your phone number is necessary to allow clearance on some House email addresses.)</FONT></TD>
      <TR> 
    </TABLE>
<INPUT TYPE=submit NAME="Submit" VALUE="Review Letter" onClick = "setUpCookies()" ><INPUT TYPE=reset NAME="Reset" VALUE="Clear Form and Start Over">
                  

  <input type="hidden" name="actionid" value="<?php echo $item; ?>">
  <input type="hidden" name="MM_insert" value="true">
  <input type="hidden" name="issuecode" value="<?php echo $issuecode; ?>">
  <input type="hidden" name="CM_bccaddress" value="<?php echo $sendfax->Fields("bcc") ?>">
   <input type="hidden" name="CM_site" value="<?php echo $sendfax->Fields("site") ?>">

</form> 