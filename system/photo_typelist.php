<?php
$modid=8;

  require("Connections/freedomrising.php");
?>
<?php
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
  // *** Delete Record: declare variables
  if (isset($MM_delete) && (isset($MM_recordId))) {
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "gallerytype";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "photo_typelist.php";
  
    if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
      $MM_editRedirectUrl = $MM_editRedirectUrl . ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
?>
<?php
  // *** Update Record: set variables
  
  if (isset($MM_update) && (isset($MM_recordId))) {
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "gallerytype";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "photo_typelist.php";
    $MM_fieldsStr = "area|value|city";
    $MM_columnsStr = "galleryname|',none,''";
  
    // create the $MM_fields and $MM_columns arrays
   $MM_fields = Explode("|", $MM_fieldsStr);
   $MM_columns = Explode("|", $MM_columnsStr);
    
    // set the form values
  for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    $MM_fields[$i+1] = $$MM_fields[$i];
    }
  
    // append the query string to the redirect URL
  if ($MM_editRedirectUrl && $MM_keepNone && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
    $MM_editRedirectUrl .= ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
// *** Update Record: set variables
  
  if (isset($MM_insert)) {
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "gallerytype";
         $MM_editRedirectUrl = "photo_typelist.php";
    $MM_fieldsStr = "area|value|city";
    $MM_columnsStr = "galleryname|',none,''";
  
    // create the $MM_fields and $MM_columns arrays
   $MM_fields = Explode("|", $MM_fieldsStr);
   $MM_columns = Explode("|", $MM_columnsStr);
    
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

$Recordset2__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset2__MMColParam = $HTTP_GET_VARS["id"];}
?>
<?php
   $Recordset1=$dbcon->Execute("SELECT * FROM gallerytype ORDER BY galleryname ASC") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?>
<?php
   $Recordset2=$dbcon->Execute("SELECT * FROM gallerytype WHERE id = " . ($Recordset2__MMColParam) . " ORDER BY galleryname ASC") or DIE($dbcon->ErrorMsg());
   $Recordset2_numRows=0;
   $Recordset2__totalRows=$Recordset2->RecordCount();
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
<?php include ("header.php");?>

<?php if ($HTTP_GET_VARS["show"] == (1)) { ?>
<h2>Gallery Types</h2>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>" >
        <table width="90%" border="0" align="center">
          <tr> 
            <td>Gallery Name</td>
            <td> <input type="text" name="area" size="50" value="<?php echo $Recordset2->Fields("galleryname")?>"> 
            </td>
          </tr>
        </table>
  <p> 
    <input type="submit" name="Submit" value="Submit">
  </p>
  
  <input type="hidden" name="MM_update" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $Recordset2->Fields("id") ?>">
</form>
<form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="submit" name="delete" value="delete">
  <input type="hidden" name="MM_delete" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $Recordset2->Fields("id") ?>">
</form>
<p>&nbsp;</p>
<?php }
/* if ($HTTP_GET_VARS["show"] == (1)) */
?>

<?php if ($HTTP_GET_VARS["show"] == (2)) { ?>
<h2>Gallery Types</h2>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>" >
        <table width="90%" border="0" align="center">
          <tr> 
            <td>Gallery Name</td>
            <td> <input type="text" name="area" size="50" value="<?php echo $Recordset2->Fields("galleryname")?>"> 
            </td>
          </tr>
        </table>
  <p> 
    <input type="submit" name="Submit" value="Submit">
  </p>
  
  <input type="hidden" name="MM_insert" value="true">
 
</form>

<p>&nbsp;</p>
<?php }
/* if ($HTTP_GET_VARS["show"] == (1)) */
?>


<?php if ($HTTP_GET_VARS["show"] == ($null)) { ?>
      <h2>Edit Gallery Type</h2>
      <p>&nbsp;</p>
<table width="90%" border="0" cellspacing="2" cellpadding="3" align="center">
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
  <tr bgcolor="#CCCCCC"> 
    <td> 
      <?php echo $Recordset1->Fields("galleryname")?>
    </td>
    <td> 
      <?php echo $Recordset1->Fields("id")?>
    </td>
    <td><A HREF="photo_typelist.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>&show=1">edit</A></td>
  </tr>
  <?php
  $Repeat1__index++;
  $Recordset1->MoveNext();
}
?>

</table>
<a href="photo_typelist.php?show=2">Add A type</a>
      <?php }
/* if ($HTTP_GET_VARS["show"] == ($null)) */
?>
  </body>
</html>
<?php
  $Recordset1->Close();
?><?php
  $Recordset2->Close();
?>
