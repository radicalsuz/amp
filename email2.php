<?php 
/*********************
07-02-2003  v3.01
Module:  email
Description:  email subscription form  that sends to a system out side of  ours ie listserve. makes a entry in email but not subsctiption
CSS: text, form
VARS: $sendemail = email to send subscription information
To Do:  declare  post vars
			   insert into contacts database
			   

*********************/ 

$mod_id = 20;
$modid=9;
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php"); 
         
 // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
?>
            <?php
// *** Insert Record: set Variables
######### INSERT RECORD  ################################## 
// *** Insert Record: set Variables
if (isset($MM_insert)){
   // $MM_editConnection = MM__STRING;
   //send subscribe email
   if ($sendemail != NULL) {  
mail ( "$sendemail","" ,"" , "From: $email\nX-Mailer: My PHP Script\n");} 
  //check for dups
   if  (isset($email))  {
  $emailcheck=$dbcon->Execute("SELECT email FROM email where email = '$email' LIMIT 1") or DIE($dbcon->ErrorMsg());
  if ($emailcheck->RecordCount() == NULL) {
   $MM_editTable  = "email";
   $MM_editRedirectUrl = "email.php?thank=1";
   $MM_fieldsStr = "lastname|value|firstname|value|organization|value|select|value|email|value|phone|value|fax|value|web|value|address|value|address2|value|city|value|state|value|zip|value|country|value|description|value";
   $MM_columnsStr = "lastname|',none,''|firstname|',none,''|organization|',none,''|type|',none,''|email|',none,''|phone|',none,''|fax|',none,''|url|',none,''|address1|',none,''|address2|',none,''|city|',none,''|state|',none,''|zip|',none,''|country|',none,''|description|',none,''";



  require ("DBConnections/insetstuff.php");
  require ("DBConnections/dataactions.php");}
  }
  header ("Location: email.php?thank=1");
  }
  
 if ($HTTP_GET_VARS["thank"] == ($null)) { ?>
        
            <form method="POST" action="<?php echo $MM_editAction?>" name="form1" onSubmit="MM_validateForm('email','','RisEmail');return document.MM_returnValue">
              
  <table border=0 cellpadding=2 cellspacing=0 align="center">
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">First Name:</td>
      <td> <input type="text" name="lastname" value="" size="32">
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Last Name</td>
      <td><input type="text" name="firstname" value="" size="32"></td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Organization:</td>
      <td> <input type="text" name="organization" value="" size="50"> </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">E-mail:</td>
      <td> <input type="text" name="email" value="<?php echo $emailsub;?>" size="32"> </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Phone:</td>
      <td> <input type="text" name="phone" value="" size="32"> </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Fax:</td>
      <td> <input type="text" name="fax" value="" size="32"> </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Web Site:</td>
      <td> <input type="text" name="web" value="" size="50"> </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Address:</td>
      <td> <input type="text" name="address" value="" size="45"> </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form"></td>
      <td> <input type="text" name="address2" value="" size="45"> </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">City:</td>
      <td> <input type="text" name="city" value="" size="32"> </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">State:</td>
      <td> <input type="text" name="state" value="" size="5">
        Zip 
        <input type="text" name="zip" value="" size="15"> </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Country:</td>
      <td> <input type="text" name="country" value="US" size="32"> </td>
    </tr>
    <tr> 
      <td nowrap align="right" valign="top" class="form">Other Information:</td>
      <td valign="baseline"> <textarea name="description" cols="35" rows="5" wrap="VIRTUAL"></textarea> 
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">&nbsp;</td>
      <td> <input type="submit" value="Sign Up for Updates" name="submit"> </td>
    </tr>
  </table>
              <input type="hidden" name="MM_insert" value="true">
			   <input type="hidden" name="sendemail" value="<?php echo $sendemail ?>">
            </form>
            <?php }

           if ($HTTP_GET_VARS["thank"] == ("1")) { ?>
<?php 
	  $mod_id = 24 ;
	  include("AMP/BaseModuleIntro.php"); ?>
<?php } //end thank you
 include("AMP/BaseFooter.php"); ?>

