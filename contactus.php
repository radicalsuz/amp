<?php 
/*********************
05-06-2003  v3.01
Module:  Contact Us
Description:  sends email to $MM_email_contact
To Do: declare post vars

*********************/ 
 include_once "Connections/jpcache-sql.php"; 
$modid =17;
$mod_id = 52;
if ($_POST[thank] == ("1")) { 
	  $mod_id = 53 ;}
include("sysfiles.php");
include("header.php");

if ($send == 1) {    
 mail ( "$MM_email_contact", "$subject", "$message", "From: $email \nX-Mailer: My PHP Script\n");
//
}

	

 if ($_POST[thank] == ($null)) { ?>
<form method="post" action="<?php $PHP_SELF."?thank=1" ; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="5" class="form">
  <tr> 
    <td valign="top">Your E-Mail:</td>
    <td><input name="email" type="text" id="email" size="40"></td>
  </tr>
  <tr> 
    <td valign="top">Subject:</td>
    <td><input name="subject" type="text" id="subject" size="40"></td>
  </tr>
  <tr valign="top"> 
    <td colspan="2"><p>Message:<br>
          <textarea name="message" cols="48" rows="25" wrap="VIRTUAL" id="message"></textarea>
          <input name="send" type="hidden" id="send" value="1">
		    <input name="thank" type="hidden" id="send" value="1">
        </p>
        <p>
          <input type="submit" name="Submit" value="Send E-Mail">
        </p></td>
  </tr>
</table>
</form>
<?php
}
 
 include("footer.php"); ?>