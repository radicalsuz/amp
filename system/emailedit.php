<?php 
 $modid=9;
  require("Connections/freedomrising.php");
 
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
 
######### INSERT RECORD  ################################## 
// *** Insert Record: set Variables
if (isset($MM_insert)){
   // $MM_editConnection = MM__STRING;
   $MM_editTable  = "email";
   $MM_fieldsStr = "lastname|value|firstname|value|organization|value|select|value|email|value|phone|value|fax|value|web|value|address1|value|address2|value|city|value|state|value|zip|value|country|value|description|value|html|value|student|value";
   $MM_columnsStr = "lastname|',none,''|firstname|',none,''|organization|',none,''|type|',none,''|email|',none,''|phone|',none,''|fax|',none,''|url|',none,''|address1|',none,''|address2|',none,''|city|',none,''|state|',none,''|zip|',none,''|country|',none,''|description|',none,''|html|none,1,0|student|none,1,0";

  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  
  
 $newrec=$dbcon->Execute("SELECT id FROM email ORDER BY id desc LIMIT 1") or DIE($dbcon->ErrorMsg());  
$recid=$newrec->Fields("id");

while (($Repeat1__numRows-- != 0) && (!$lists->EOF)) 
   { 
if (isset($HTTP_POST_VARS[$lists->Fields("id")]))  {
$listid = $lists->Fields("id"); 

 $MM_editTable  = "subscription";
  $MM_fieldsStr = "recid|value|listid|value";
   $MM_columnsStr = "userid|none,none,NULL|listid|none,none,NULL"; 
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
	}
	
	$Repeat1__index++;
  $lists->MoveNext();
  }

	 $MM_editRedirectUrl = "email_list.php";
	 header ("Location: $MM_editRedirectUrl");
   }// end insert
   
######### UPDATE RECORD  ##################################  
if  (isset($MM_update)){  //start update
  $MM_editTable  = "email";
    $MM_editColumn = "id";  
	$MM_recordId = "" . $MM_recordId . "";
   $MM_fieldsStr = "lastname|value|firstname|value|organization|value|select|value|email|value|phone|value|fax|value|web|value|address1|value|address2|value|city|value|state|value|zip|value|country|value|description|value|html|value|student|value";
   $MM_columnsStr = "lastname|',none,''|firstname|',none,''|organization|',none,''|type|',none,''|email|',none,''|phone|',none,''|fax|',none,''|url|',none,''|address1|',none,''|address2|',none,''|city|',none,''|state|',none,''|zip|',none,''|country|',none,''|description|',none,''|html|none,1,0|student|none,1,0";
   require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");

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
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");}


	if ($instance == ($null)) { //start delete
	
	$listid = $lists->Fields("id");
$supvar= "b".$listid;
	 $MM_recordId = ($HTTP_POST_VARS["$supvar"]);
	if  ($MM_recordId != $null){
	$MM_delete = 1;
  $MM_editColumn = "id";  
$MM_editTable  = "subscription";
  
	require ("../Connections/insetstuff.php");
   require ("../Connections/dataactions.php");}
}//end deletet	
	 
	
	$Repeat1__index++;
  $lists->MoveNext();
  } //end repeat 
 
   header ("Location: emailedit.php");
  } 
 
  //end update   
  
######### DELETE RECORD  ##################################
 if  (isset($MM_delete)){  
   $dbcon->Execute("DELETE FROM subscription WHERE userid = $MM_recordId") or DIE($dbcon->ErrorMsg());
   $dbcon->Execute("DELETE FROM email WHERE id = $MM_recordId") or DIE($dbcon->ErrorMsg());
   
   header ("Location: email_list.php");
    }//end delete
################POPULATE FORM  ######################
   $Recordset1__MMColParam = "8000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
$Recordset1=$dbcon->Execute("SELECT * FROM email WHERE id = $Recordset1__MMColParam") or DIE($dbcon->ErrorMsg());
$state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
?>
				 <?php include("header.php");?> <?php 
 ################ FORM DATA  ######################				  
				  if ($HTTP_GET_VARS["thank"] == ($null)) { ?>
      <form method="POST" action="<?php echo $MM_editAction?>" name="form1">
 
        <table width="98%" border=0 align="center" cellpadding=2 cellspacing=0>
          <tr valign="baseline" class="banner"> 
            <td colspan="2" align="right" nowrap class="form"><div align="left">Add/Edit 
                User List Subscription</div></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">First Name:</td>
            <td><input type="text" name="firstname" value="<?php echo $Recordset1->Fields("firstname")?>" size="32"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Last Name:</td>
            <td><input type="text" name="lastname" value="<?php echo $Recordset1->Fields("lastname")?>" size="32"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">E-mail:</td>
            <td> <input type="text" name="email" value="<?php if (empty($id)) {echo $email;} else  {echo $Recordset1->Fields("email");} ?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">City:</td>
            <td> <input type="text" name="city" value="<?php echo $Recordset1->Fields("city")?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">State:</td>
            <td> <select name="state" id="state">
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
            <td align="right" nowrap class="form">Country:</td>
            <td> <input type="text" name="country" value="<?php echo $Recordset1->Fields("country")?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" nowrap class="form">Student</td>
            <td><input type="checkbox" name="student" value="1" <?php if (($Recordset1->Fields("student")) == ("1")) {echo "checked";}?> ></td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" nowrap class="form">Receive E-Mails in HTML</td>
            <td><input type="checkbox" name="html" value="1" <?php if ($Recordset1->Fields("html") ==1) {echo "checked";}?>></td>
          </tr>
          <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap class="form"><div align="center"><strong><br>
                Select the mailings you'd wish to receive</strong></div></td>
          </tr>
          <?php while (($Repeat1__numRows-- != 0) && (!$lists->EOF)) 
   { 

$instance=$dbcon->Execute("SELECT id FROM subscription WHERE userid = ".$Recordset1__MMColParam." and listid= ".($lists->Fields("id"))." LIMIT 1") or DIE($dbcon->ErrorMsg());
		$inst=$instance->Fields("id");
			$instance->Close();?>
          <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td colspan="2" width="20%"> <div align="right"><?php echo $lists->Fields("name"); ?></div></td>
                  <td ><input name="<?php echo $lists->Fields("id"); ?>" type="checkbox" id="<?php echo $lists->Fields("id"); ?>3" value="<?php echo ("$inst"); ?><?php if (empty($inst)){
						echo "500"; }?>" <?php 
			
			if (isset($inst)){ echo "checked";} ?>> <input name="b<?php echo ($lists->Fields("id")); ?>" type="hidden" value="<?php echo ("$inst"); ?>"> 
                  </td>
                </tr>
              </table></td>
          </tr>
          <?php $Repeat1__index++;
  $lists->MoveNext();
}?>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td><input type="submit" name="<?php if (($HTTP_GET_VARS["id"])== ($null)) {echo "MM_insert";} else {echo "MM_update";}?>" value="Submit"> 
              <input type="submit" name="MM_delete" value="Delete" onClick="return confirmSubmit('Are you sure you want to DELETE this record?')"> <input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>"> 
            </td>
          </tr>
        </table>
  
</form>
<?php }
 
$state->Close();
  $Recordset1->Close();
	  
?>

<?php include("footer.php"); ?>