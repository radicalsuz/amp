<?php  
/*********************
07-08-2003  v3.01
Module:  email
Description:  email subscription form  (has more fields and enters into contacts)
CSS: text, form
VARS: $studenton = displays the student box
			$send = sends a link to edit  the subscriptions
To Do:  declare  post vars
				country drop downs
				verify required

*********************/ 
ob_start();
// 
$mod_id = 20;
$modid=9;
$source = 11;
$enteredby =2;
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php"); 
  include_once("dropdown.php");
include_once( 'AMP/System/Email.inc.php');
$dev_message = &new AMPSystem_Email( );
$dev_message->setRecipient( 'seth@radicaldesigns.org, austin@radicaldesigns.org' );
$dev_message->setSubject( 'email3 request from '.AMP_SITE_URL.$_SERVER['REQUEST_URI']);
$message = '$_SERVER = '."\n";
$message .= print_r($_SERVER, true)."\n";
$message .= '$_REQUEST = '."\n";
$message .= print_r($_REQUEST, true)."\n";
$dev_message->setMessage( $message );
$dev_message->execute( );

 ##################get the list of lists ########################
 $lists=$dbcon->Execute("SELECT * FROM lists where publish=1 ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
$lists_numRows=0;
$lists__totalRows=$lists->RecordCount();
$Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $lists_numRows = $lists_numRows + $Repeat1__numRows;
 
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
###############Check to see if list is already in system #################
if  (isset($emailsub))  {
  $emailcheck=$dbcon->Execute("SELECT email FROM email where email = '$emailsub' LIMIT 1") or DIE($dbcon->ErrorMsg());
  if ($emailcheck->RecordCount() != $null) {echo "<p align=center><b >This email is is already subscribed <br> To update your subscription please click the \"Update\" button below</b></p>";
  $emailsub2=$emailsub;
  $emailsub="";
	}
	 $emailcheck->Close(); }
  
  
#######################send edit link##################
if (isset($send)){  
  $emailsend=$dbcon->Execute("SELECT email, id FROM email where email = '$emailedit' LIMIT 1") or DIE($dbcon->ErrorMsg());
 
 if ($emailsend->RecordCount() == $null) {echo "This email is not valid.";}
 else {
 $sendtox =$emailsend->Fields("email");
 $idnum = (($emailsend->Fields("id"))*3+792875);
 if ($SendConfirm == 1) {
 $body = "Please follow the following link to update your information\n ".$Web_url."email.php?id=".$emailsend->Fields("id")."&token=".$idnum;
 mail("$sendtox", "Update Your Subscription", "$body",  "From: ".AMPSystem_Email::sanitize($MM_email_from)."\nX-Mailer: My PHP Script\n");
 echo "Please check your email for a link to edit you subscription";}
 else {
 $goto = $Web_url."email.php?id=".$emailsend->Fields("id")."&token=".$idnum;
 header ("Location: $goto");
 }
 
}
$emailsend->Close();
}
 

######### INSERT RECORD  ################################## 
// *** Insert Record: set Variables
if (isset($MM_insert)){
   // $MM_editConnection = MM__STRING;
   $MM_editTable  = "email";
   $MM_fieldsStr = "lastname|value|firstname|value|organization|value|select|value|email|value|phone|value|fax|value|web|value|address1|value|address2|value|city|value|state|value|zip|value|country|value|description|value|html|value|student|value|position|value";
   $MM_columnsStr = "lastname|',none,''|firstname|',none,''|organization|',none,''|type|',none,''|email|',none,''|phone|',none,''|fax|',none,''|url|',none,''|address1|',none,''|address2|',none,''|city|',none,''|state|',none,''|zip|',none,''|country|',none,''|description|',none,''|html|none,1,0|student|none,1,0|position|',none,''";

  require ("DBConnections/insetstuff.php");
  require ("DBConnections/dataactions.php");
  
   /*
    * contact2 code was here, now removed 2006-02-28 AP
  */
  
 $newrec=$dbcon->Execute("SELECT id FROM email ORDER BY id desc LIMIT 1") or DIE($dbcon->ErrorMsg());  
$recid=$newrec->Fields("id");

while (($Repeat1__numRows-- != 0) && (!$lists->EOF)) 
   { 
if (isset($HTTP_POST_VARS[$lists->Fields("id")]))  {
$listid = $lists->Fields("id"); 

 $MM_editTable  = "subscription";
  $MM_fieldsStr = "recid|value|listid|value";
   $MM_columnsStr = "userid|none,none,NULL|listid|none,none,NULL"; 
	require ("DBConnections/insetstuff.php");
    require ("DBConnections/dataactions.php");
	}
	
	$Repeat1__index++;
  $lists->MoveNext();
  }

	 $MM_editRedirectUrl = "email.php?thank=1";
	
	 header ("Location: $MM_editRedirectUrl");
   }// end insert
   
######### UPDATE RECORD  ##################################  
if  (isset($MM_update)){  //start update
  $MM_editTable  = "email";
    $MM_editColumn = "id";  
	$MM_recordId = "" . $MM_recordId . "";
   $MM_fieldsStr = "lastname|value|firstname|value|organization|value|select|value|email|value|phone|value|fax|value|web|value|address1|value|address2|value|city|value|state|value|zip|value|country|value|description|value|html|value|student|value";
   $MM_columnsStr = "lastname|',none,''|firstname|',none,''|organization|',none,''|type|',none,''|email|',none,''|phone|',none,''|fax|',none,''|url|',none,''|address1|',none,''|address2|',none,''|city|',none,''|state|',none,''|zip|',none,''|country|',none,''|description|',none,''|html|none,1,0|student|none,1,0";
   require ("DBConnections/insetstuff.php");
    require ("DBConnections/dataactions.php");

$userid= $MM_recordId;
$MM_update = ($null);
while (($Repeat1__numRows-- != 0) && (!$lists->EOF)) 
   { //start repeat
  $instance = ($HTTP_POST_VARS[$lists->Fields("id")]);

  if ($instance == 500){ //insert
  $listid = $lists->Fields("id");
  $MM_insert=1; 

	$MM_editTable  = "subscription";
  $MM_fieldsStr = "listid|value|userid|value";
   $MM_columnsStr = "listid|none,none,NULL|userid|none,none,NULL"; 
	require ("DBConnections/insetstuff.php");
    require ("DBConnections/dataactions.php");}


	if ($instance == ($null)) { //start delete
	
	$listid = $lists4->Fields("id");
$supvar= "b".$listid;
	 $MM_recordId = ($HTTP_POST_VARS["$supvar"]);
	if  ($MM_recordId != $null){
	$MM_delete = 1;
  $MM_editColumn = "id";  
$MM_editTable  = "subscription";
  
	require ("DBConnections/insetstuff.php");
   require ("DBConnections/dataactions.php");}
}//end deletet	
	 
	
	$Repeat1__index++;
  $lists->MoveNext();
  } //end repeat 
 
   header ("Location: email.php?thank=2");
  } 
 
  //end update   
  
######### DELETE RECORD  ##################################
 if  (isset($MM_delete)){  
   $dbcon->Execute("DELETE FROM subscription WHERE userid = $MM_recordId") or DIE($dbcon->ErrorMsg());
   $dbcon->Execute("DELETE FROM email WHERE id = $MM_recordId") or DIE($dbcon->ErrorMsg());
   
   header ("Location: email.php?thank=3");
    }//end delete
################POPULATE FORM  ######################
  if (($HTTP_GET_VARS["token"]) == (($HTTP_GET_VARS["id"])*3+792875)) {
  if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];
  $Recordset1__MMColParam2 = $Recordset1__MMColParam;}}
   else {$Recordset1__MMColParam = "8000000";}
$Recordset1=$dbcon->Execute("SELECT * FROM email WHERE id = $Recordset1__MMColParam") or DIE($dbcon->ErrorMsg());
$state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
?>
				  <?php 
 ################ FORM DATA  ######################				  
				  if ($HTTP_GET_VARS["thank"] == ($null)) { ?>
      <form method="POST" action="<?php echo $MM_editAction?>" name="form1" class="form">

  <table class="form" width="100%" border=0 align="center" cellpadding=2 cellspacing=0>
      </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">E-mail:&nbsp;&nbsp;</td>
      <td> <input type="text" name="email" value="<?php if (empty($id)) {echo $emailsub;} else  {echo $Recordset1->Fields("email");} ?>" size="30" style="width: 262px;"> 
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">First Name:&nbsp;&nbsp;</td>
      <td> <input type="text" name="firstname" value="<?php echo $Recordset1->Fields("firstname")?>" size="30" style="width: 262px;"> 
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Last Name:&nbsp;&nbsp;</td>
      <td><input type="text" name="lastname" value="<?php echo $Recordset1->Fields("lastname")?>" size="30" style="width: 262px;"></td>
</tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Organization:&nbsp;&nbsp;</td>
      <td><input name="organization" type="text" id="organization" value="<?php echo $Recordset1->Fields("organization")?>" size="30" style="width: 262px;"></td>
</tr>
 <tr valign="baseline"> 
      <td nowrap align="right" class="form">Position/Title:&nbsp;&nbsp;</td>
      <td><input name="position" type="text" id="position" value="<?php echo $Recordset1->Fields("position")?>" size="30" style="width: 262px;"></td>
</tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Address:&nbsp;&nbsp;</td>
      <td> <input name="address1" type="text" id="address1" value="<?php echo $Recordset1->Fields("address1")?>" size="30" style="width: 262px;"> 
      </td>
    </tr>
	    <tr valign="baseline"> 
      <td nowrap align="right" class="form">&nbsp;&nbsp;</td>
      <td> <input name="address2" type="text" id="address2" value="<?php echo $Recordset1->Fields("address2")?>" size="30" style="width: 262px;"> 
      </td>
    </tr>
	
	   <tr valign="baseline"> 
      <td nowrap align="right" class="form">City:&nbsp;&nbsp;</td>
      <td> <input type="text" name="city" value="<?php echo $Recordset1->Fields("city")?>" size="30" style="width: 262px;"> 
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">State:&nbsp;&nbsp;</td>
      <td class="form"> 
        <select name="state" id="state">
                <option value = "">Select State</option>
                <?php    if ($state__totalRows > 0){
    $state__index=0;
    $state->MoveFirst();
    WHILE ($state__index < $state__totalRows){
?>
                <option value="<?php echo  $state->Fields("id")?>" <?php if ($state->Fields("id")==$Recordset1->Fields("state")) echo "SELECTED";?>> 
                <?php echo  $state->Fields("statename");?> </option>
                <?php
      $state->MoveNext();
      $state__index++;
    }
    $state__index=0;  
    $state->MoveFirst();
  } ?>
              </select>
        Zip 
        <input type="text" name="zip" value="<?php echo $Recordset1->Fields("zip")?>" size="15"> 
      </td>
    </tr>
    <tr valign="baseline"> 
      <td align="right" nowrap class="form">Country:&nbsp;&nbsp;</td>
      <td> <select name="country" id="select">
          <?php echo $countryDropDown2; ?> </select> 
	  <tr valign="baseline"> 
      <td nowrap align="right" class="form">Phone:&nbsp;&nbsp;</td>
      <td> <input name="phone" type="text" id="phone" value="<?php echo $Recordset1->Fields("phone")?>" size="30" style="width: 262px;"> 
      </td>
    </tr>
	<tr valign="baseline"> 
      <td nowrap align="right" class="form">Fax:&nbsp;&nbsp;</td>
      <td> <input name="fax" type="text" id="fax" value="<?php echo $Recordset1->Fields("fax")?>" size="30" style="width: 262px;"> 
      </td>
    </tr>
      </td>
    </tr>
	
	<?php if ($studenton == 1){ ?>
    <tr valign="baseline"> 
      <td align="right" nowrap class="form">&nbsp;</td>
      <td><input type="checkbox" name="student" value="1" <?php if (($Recordset1->Fields("student")) == ("1")) {echo "checked";}?> >
        Student</td>
    </tr>
    <tr valign="baseline"> 
	<?php } ?>
      <td align="right" nowrap class="form">&nbsp;</td>
      <td><input type="checkbox" name="html" value="1" <?php if ($Recordset1->Fields("html") ==1) {echo "checked";}?>>
        Receive E-Mails in HTML</td>
    </tr>
    <tr valign="baseline"> 
      <td colspan="2" nowrap class="form"><br>
Select  E-Mail Alerts:
      <br>
          </td>
    </tr>
    <?php while (($Repeat1__numRows-- != 0) && (!$lists->EOF)) 
   { 

$instance=$dbcon->Execute("SELECT id FROM subscription WHERE userid = ".$Recordset1__MMColParam." and listid= ".($lists->Fields("id"))." LIMIT 1") or DIE($dbcon->ErrorMsg());
		$inst=$instance->Fields("id");
			$instance->Close();?>
    <tr valign="baseline"> 
      <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
          <tr> 
            <td> 
                <input name="<?php echo $lists->Fields("id"); ?>" type="checkbox" id="<?php echo $lists->Fields("id"); ?>3" value="<?php echo ("$inst"); ?><?php if (empty($inst)){
						echo "500"; }?>" <?php 
			if ($id == NULL){ echo "checked";}
			if (isset($inst)){ echo "checked";} ?>>
              
              <b><?php echo $lists->Fields("name"); ?>:</b>&nbsp;<?php echo $lists->Fields("description"); ?> 
              <input name="b<?php echo ($lists->Fields("id")); ?>" type="hidden" value="<?php echo ("$inst"); ?>"> 
            </td>
          </tr>
        </table></td>
    </tr>
    <?php $Repeat1__index++;
  $lists->MoveNext();
}?>
    <tr valign="baseline"> 
      <td colspan="2" align="right" nowrap class="form"><div align="center"><br>
          <input type="submit" name="<?php if (($HTTP_GET_VARS["id"])== ($null)) {echo "MM_insert";} else {echo "MM_update";}?>" value="Subscribe">
          &nbsp;&nbsp; 
         <?php if (($HTTP_GET_VARS["id"])!= ($null)) { ?> <input type="submit" name="MM_delete" value="Remove from All Lists"><?php }?>
          <input type="hidden" name="MM_recordId" value="<?php echo $Recordset1__MMColParam2 ?>">
        &nbsp;&nbsp;</td>
    </tr>
    <?php if ($hidesub != 1) { ?><tr valign="baseline"> 
      <td nowrap align="right" class="form">&nbsp;&nbsp;</td>
      <td><p><br>
        </p>
        <p>&nbsp; </p></td>
    </tr>
    <tr valign="baseline"> 
      <td colspan="2" align="right" nowrap bgcolor="#666666" class="form"><div align="center"> 
          <strong><font color="#FFFFFF" size="3">Unsubscribe or Change Your Subscriptions</font></strong> 
        &nbsp;&nbsp;</td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" class="form">Email Address</td>
      <td><input name="emailedit" type="text" id="emailedit" value="<?php echo $emailsub2; ?>"> <input name="send" type="submit" id="send" value="Update"></td>
    </tr><?php } ?>
  </table>
  
</form>
<?php }
 
 ############# thank you 1 ###############
 if ($HTTP_GET_VARS["thank"] == ("1")) { ?>
<?php 
	  $mod_id = 24 ;
	  include("AMP/BaseModuleIntro.php"); ?>
<?php } //end thank you
	  
 ############# thank you 2###############
 if ($HTTP_GET_VARS["thank"] == ("2")) { ?>
<h2>Thank You For Updating Your Information</h2>
      <?php } //end thank you
 
 ############# thank you 3###############
 if ($HTTP_GET_VARS["thank"] == ("3")) { ?>
<h2>You have been removed from our lists</h2>
      <?php } //end thank you	  
	  

 $state->Close();
  $Recordset1->Close();?>
<?php include("AMP/BaseFooter.php"); ?>
