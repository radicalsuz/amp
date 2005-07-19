<?php
$modid=38;
  require("Connections/freedomrising.php");
$formtitle = "Messages";
$tablein = "message";
$filename = "message.php";
 // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();
?>
<?php

 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
if (isset($MM_insert)){
if ($toid != NULL) {
$mailusers=$dbcon->Execute("SELECT email FROM users where id=$toid") or DIE($dbcon->ErrorMsg());
$mail = $mailusers->Fields("email");
if ($mail != NULL) {
$mailtext = "From: $messagefrom \n Phone: $phone \n Email: $email \n Message: $message \n  ";
   mail ( "$mail", "new message from the front desk", "$mailtext", "From: info@mattgonzalez.com\nX-Mailer: My PHP Script\n"); 
   $recivied =1;}
   }
}
    $MM_editTable  = $tablein;
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "index.php";
    
   if (isset($MM_insert)){
   $MM_fieldsStr = "messagefrom|value|phone|value|email|value|toid|value|toother|value|message|value|date|value|recivied|value";
    $MM_columnsStr = "messagefrom|',none,''|phone|',none,''|email|',none,''|toid|',none,''|toother|',none,''|message|',none,''|date|NOW(),NOW(),NOW()|recivied|',none,''";}
	else {
	$MM_fieldsStr = "messagefrom|value|phone|value|email|value|toid|value|toother|value|message|value|recivied|value";
    $MM_columnsStr = "messagefrom|',none,''|phone|',none,''|email|',none,''|toid|',none,''|toother|',none,''|message|',none,''|recivied|',none,''";}
  
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }

$Recordset2__MMColParam = "900000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset2__MMColParam = $HTTP_GET_VARS["id"];}
?>
<?php
   $new=$dbcon->Execute("SELECT message.messagefrom, message.toid, message.toother, message.id, message.date, users.name FROM message, users where message.toid =  users.id and recivied != 1 order by date desc") or DIE($dbcon->ErrorMsg());
 $old=$dbcon->Execute("SELECT message.messagefrom, message.toid, message.toother, message.id, message.date, users.name FROM message, users where message.toid =  users.id and recivied = 1 order by date desc") or DIE($dbcon->ErrorMsg());
  $allusers=$dbcon->Execute("SELECT id, email, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $allusers_numRows=0;
   $allusers__totalRows=$allusers->RecordCount();
   $called=$dbcon->Execute("SELECT * FROM $tablein WHERE id = " . ($Recordset2__MMColParam) . "") or DIE($dbcon->ErrorMsg());
?>
<?php $MM_paramName = ""; ?>
<?php
// *** Go To Record and Move To Record: create strings for maintaining URL and Form parameters

// create the list of parameters which should not be maintained
$MM_removeList = "&index=";
if ($MM_paramName != "") $MM_removeList .= "&".strtolower($MM_paramName)."=";
$MM_keepURL="";
$MM_keepForm="";
$MM_keepBoth="";
$MM_keepNone="";

// add the URL parameters to the MM_keepURL string
reset ($HTTP_GET_VARS);
while (list ($key, $val) = each ($HTTP_GET_VARS)) {
	$nextItem = "&".strtolower($key)."=";
	if (!stristr($MM_removeList, $nextItem)) {
		$MM_keepURL .= "&".$key."=".urlencode($val);
	}
}

// add the URL parameters to the MM_keepURL string
if(isset($HTTP_POST_VARS)){
	reset ($HTTP_POST_VARS);
	while (list ($key, $val) = each ($HTTP_POST_VARS)) {
		$nextItem = "&".strtolower($key)."=";
		if (!stristr($MM_removeList, $nextItem)) {
			$MM_keepForm .= "&".$key."=".urlencode($val);
		}
	}
}

// create the Form + URL string and remove the intial '&' from each of the strings
$MM_keepBoth = $MM_keepURL."&".$MM_keepForm;
if (strlen($MM_keepBoth) > 0) $MM_keepBoth = substr($MM_keepBoth, 1);
if (strlen($MM_keepURL) > 0)  $MM_keepURL = substr($MM_keepURL, 1);
if (strlen($MM_keepForm) > 0) $MM_keepForm = substr($MM_keepForm, 1);
?>


<?php include ("header.php");?>
      <h2><?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "Add";} else {echo "Edit";} ?> <?php echo $formtitle ;?></h2>
      <form name="form1" method="POST" action="<?php echo $MM_editAction?>" >
        <table width="90%" border="0" align="center">
         
		  <tr><td></td>
            <td> <strong><?php echo dodatetime($called->Fields("date"),("n/j/y h:m"))?></strong></td>
          </tr><tr> 
            <td> From</td>
            <td> <input name="messagefrom" type="text" id="from" value="<?php echo $called->Fields("messagefrom")?>" size="50"> 
            </td>
          </tr>
          <tr> 
            <td>Phone Number</td>
            <td><input name="phone" type="text" id="phone" value="<?php echo $called->Fields("phone")?>" size="50"></td>
          </tr>
          <tr> 
            <td>Email</td>
            <td><input name="email" type="text" id="email" value="<?php echo $called->Fields("email")?>" size="50"></td>
          </tr>
          <tr> 
            <td>To</td>
            <td><select name="toid" id="toid">
		<option>Select User</option>
                <?php
  if ($allusers__totalRows > 0){
    $allusers__index=0;
    $allusers->MoveFirst();
    WHILE ($allusers__index < $allusers__totalRows){
?>
                <OPTION VALUE="<?php echo $allusers->Fields("id")?>"<?php if ($allusers->Fields("id")==$called->Fields("toid")) echo "SELECTED";?>>
                <?php echo $allusers->Fields("name");?>
                </OPTION>
                <?php
      $allusers->MoveNext();
      $allusers__index++;
    }
    $allusers__index=0;  
    $allusers->MoveFirst();
  }
?>  </select></td>
          </tr>
          <tr> 
            <td>To (non-system)</td>
            <td><input name="toother" type="text" id="toother" value="<?php echo $called->Fields("toother")?>" size="50"></td>
          </tr>
          <tr> 
            <td>Message</td>
            <td><textarea name="message" cols="50" rows="10" wrap="VIRTUAL" id="message"><?php echo $called->Fields("message")?></textarea></td>
          </tr>
          <tr> 
            <td>Received</td>
            <td><input name="recivied" type="checkbox" id="recivied" value="1"></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
        <p> 
          <input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes">
          <input name="MM_delete" type="submit" value="Delete Record" onClick="return confirmSubmit('Are you sure you want to DELETE this record?')">
          <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
          <br>
          <br>
          <a href="<?php echo $filename;?>">Add 
          A <?php echo $formtitle ;?></a> 
      </form><?php if (AMP_Authorized( AMP_PERMISSION_MESSAGES_ADMIN )){ ?>
      <h2>Messages that have not been marked as received</h2>
    
     
      <table width="90%" border="0" cellspacing="2" cellpadding="3" align="center">
        <tr class="intitle"> 
          <td>From</td>
          <td>To</td>
          <td>Date</td>
          <td>&nbsp;</td>
        </tr>
        <?php while (!$new->EOF)
   { 

?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $new->Fields("messagefrom")?> </td>
          <td> <?php echo $new->Fields("name")?> <?php echo $new->Fields("toother")?>  </td>
          <td><?php echo dodatetime($new->Fields("date"),("n/j/y h:m"))?></td>
          <td><A HREF="<?php echo $filename;?>?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$new->Fields("id") ?>">edit</A></td>
        </tr>
        <?php
  
  $new->MoveNext();
}
?>
      </table>
  <?php } 
  if ( AMP_Authorized( AMP_PERMISSION_MESSAGES_ADMIN )) { ?>

      <h2>Messages that have been received or sent as email</h2>
      <table width="90%" border="0" cellspacing="2" cellpadding="3" align="center">
        <tr class="intitle"> 
          <td>From</td>
          <td>To</td>
          <td>Date</td>
          <td>&nbsp;</td>
        </tr>
        <?php while (!$old->EOF)
   { 

?>
        <tr bgcolor="#CCCCCC"> 
              <td> <?php echo $old->Fields("messagefrom")?> </td>
          <td> <?php echo $old->Fields("name")?> <?php echo $old->Fields("toother")?>  </td>
          <td><?php echo dodatetime($old->Fields("date"),("n/j/y h:m"))?></td>
          <td><A HREF="<?php echo $filename;?>?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$old->Fields("id") ?>">edit</A></td>
        </tr>
        <?php
  
  $old->MoveNext();
}
?>
      </table><?php } ?>
      <p>&nbsp;</p>
      <?php
  include ("footer.php") ;?>
