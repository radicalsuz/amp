<?php
  require("Connections/freedomrising.php");
?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();
?><?php
// *** Insert Record: set Variables

if (isset($MM_insert)){

   // $MM_editConnection = MM__STRING;
   $MM_editTable  = "users";
   $MM_editRedirectUrl = "user_list.php";
   $MM_fieldsStr = "user|value|passwordx|value|userlevel|value|email|value";
   $MM_columnsStr = "name|',none,''|password|',none,''|permission|none,none,NULL|email|',none,''";

  // create the $MM_fields and $MM_columns arrays
   $MM_fields = explode("|", $MM_fieldsStr);
   $MM_columns = explode("|", $MM_columnsStr);
  
  // set the form values
  for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    $MM_fields[$i+1] = $$MM_fields[$i];
 }

  // append the query string to the redirect URL
  if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
    $MM_editRedirectUrl .= ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
  }
}
require ("../Connections/dataactions.php");
ob_end_flush();
?>
<?php
   $username=$dbcon->Execute("SELECT * FROM users") or DIE($dbcon->ErrorMsg());
   $username_numRows=0;
   $username__totalRows=$username->RecordCount();

   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $username_numRows = $username_numRows + $Repeat1__numRows;
?><?php $MM_paramName = ""; ?>
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
   $Recordset2=$dbcon->Execute("SELECT id, name FROM per_group") or DIE($dbcon->ErrorMsg());
   $Recordset2_numRows=0;
   $Recordset2__totalRows=$Recordset2->RecordCount();
?><?php include ("header.php"); ?>

<h2><?php echo helpme(""); ?>Users</h2>
<table border="0" cellspacing="0" cellpadding="0" width="90%" align="center">
  <tr> 
    <td><b>name</b></td>
    <td><b>access level</b></td>
   
    <td>&nbsp;</td>
  </tr>
  <?php while (($Repeat1__numRows-- != 0) && (!$username->EOF)) 
   { 
?>
  <tr> 
    <td> 
      <?php echo $username->Fields("name")?>
    </td>
    <td> 
      <?php echo $username->Fields("permission")?>
    </td>
   
    <td><A HREF="user_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$username->Fields("id") ?>">edit</A></td>
  </tr>
  <?php
  $Repeat1__index++;
  $username->MoveNext();
}
?>
</table>
<h2>Add User</h2>
<form method="POST" action="<?php echo $MM_editAction?>" name="form1">
        <table border=0 cellpadding=2 cellspacing=0 align="center">
          <tr valign="baseline"> 
            <td nowrap align="right">Name:</td>
            <td> <input type="text" name="user" value="" size="32"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right">Password:</td>
            <td> <input type="password" name="passwordx" value="" size="32"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right">Access Level:</td>
            <td> <select name="userlevel">
                <option value="">Set Permission</option>
                <?php
  if ($Recordset2__totalRows > 0){
    $Recordset2__index=0;
    $Recordset2->MoveFirst();
    WHILE ($Recordset2__index < $Recordset2__totalRows){
?>
                <OPTION VALUE="<?php echo  $Recordset2->Fields("id")?>"> 
                <?php echo  $Recordset2->Fields("name");?> </OPTION>
                <?php
      $Recordset2->MoveNext();
      $Recordset2__index++;
    }
    $Recordset2__index=0;  
    $Recordset2->MoveFirst();
  }
?>
              </select> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right">email</td>
            <td><input name="email" type="password" id="email" value="" size="32"> </td>
          </tr>
          <tr valign="baseline">
            <td nowrap align="right">&nbsp;</td>
            <td><input type="submit" value="Insert Record" name="submit"></td>
          </tr>
        </table>
  <input type="hidden" name="MM_insert" value="true">
</form>
<p>&nbsp;</p>
<?php include("footer.php"); ?>
