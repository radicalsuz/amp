<?php
$modid=4;
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
   $MM_editTable  = "faqtype";
   $MM_editRedirectUrl = "faqtype_list.php";
   $MM_fieldsStr = "textfield|value|checkbox2|value";
   $MM_columnsStr = "type|',none,''|uselink|none,1,0";

  // create the $MM_fields and $MM_columns arrays
   $MM_fields = explode("|", $MM_fieldsStr);
   $MM_columns = explode("|", $MM_columnsStr);
  
  // set the form values
  for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    $MM_fields[$i+1] = $$MM_fields[$i];
 }

  // append the query string to the redirect URL
  if ($MM_editRedirectUrl && $MM_keepNone && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
    $MM_editRedirectUrl .= ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
  }
}
require ("../Connections/dataactions.php");
ob_end_flush();

   $Recordset1=$dbcon->Execute("SELECT * FROM faqtype ORDER BY type ASC") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?>
<?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $Recordset1_numRows = $Recordset1_numRows + $Repeat1__numRows;
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
<body>
<?php include("header.php"); ?>
<?php if ($HTTP_GET_VARS["show"] == (1)) { ?>
<h2>Add FAQ Type </h2>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>">
  <p>FAQ Type 
    <input type="text" name="textfield" size="40">
  </p>
  <p>Publish 
    <input type="checkbox" name="checkbox2" value="checkbox">
  </p>
  <p> 
    <input type="submit" name="Submit" value="Submit">
  </p>
  <input type="hidden" name="MM_insert" value="true">
</form>
<?php }
/* if ($HTTP_GET_VARS["show"] == (1)) */
?>
<h2>&nbsp;</h2>
<?php if ($HTTP_GET_VARS["show"] == ($null)) { ?>
<h2>Faq Types</h2>
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td>Type</td>
    <td>ID</td>
    <td>Publish</td>
    <td>&nbsp;</td>
  </tr>
  <?php while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
  <tr bgcolor="#CCCCCC"> 
    <td> 
      <?php echo $Recordset1->Fields("type")?>
    </td>
    <td> 
      <?php echo $Recordset1->Fields("id")?>
    </td>
    <td> 
      <input <?php If (($Recordset1->Fields("uselink")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox" value="1">
    </td>
    <td><A HREF="faqtype_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>">edit</A></td>
  </tr>
  <?php
  $Repeat1__index++;
  $Recordset1->MoveNext();
}
?>
</table>
<p><a href="faqtype_list.php?show=1">Add Faq Type</a></p>
<?php }
/* if ($HTTP_GET_VARS["show"] == ($null)) */
?>
<p> 
  <?php
  $Recordset1->Close();
?>
</p>
