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
?>
<?php
  // *** Update Record: set variables
  
  if (isset($MM_update) && (isset($MM_recordId))) {
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "faqtype";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "faqtype_list.php";
    $MM_fieldsStr = "textfield2|value|checkbox2|value";
    $MM_columnsStr = "type|',none,''|uselink|none,1,0";
  
    // create the $MM_fields and $MM_columns arrays
   $MM_fields = Explode("|", $MM_fieldsStr);
   $MM_columns = Explode("|", $MM_columnsStr);
    
    // set the form values
  for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    $MM_fields[$i+1] = $$MM_fields[$i];
    }
  
    // append the query string to the redirect URL
  if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
    $MM_editRedirectUrl .= ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
  ?>
<?php
  // *** Delete Record: declare variables
  if (isset($MM_delete) && (isset($MM_recordId))) {
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "faqtype";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "faqtype_list.php";
  
    if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
      $MM_editRedirectUrl = $MM_editRedirectUrl . ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
  require ("../Connections/dataactions.php");
  ob_end_flush();
?>
<?php
$calledfaq__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$calledfaq__MMColParam = $HTTP_GET_VARS["id"];}
?>
<?php
   $calledfaq=$dbcon->Execute("SELECT * FROM faqtype WHERE id = " . ($calledfaq__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $calledfaq_numRows=0;
   $calledfaq__totalRows=$calledfaq->RecordCount();
?>
<?php include("header.php"); ?>
<h2>Edit FAQ Type</h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form2">
  <p>Type 
    <input type="text" name="textfield2" value="<?php echo $calledfaq->Fields("type")?>">
  </p>
  <p>Use Link 
    <input <?php If (($calledfaq->Fields("uselink")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox2" value="1">
    <br>
    <input type="submit" name="Submit2" value="Submit">
    <input type="hidden" name="MM_update" value="true">
    <input type="hidden" name="MM_recordId" value="<?php echo $calledfaq->Fields("id") ?>">
</form>
<form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="submit" name="Submit" value="Delete">
  <input type="hidden" name="MM_delete" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $calledfaq->Fields("id") ?>">
</form>
<?php
  $calledfaq->Close();
?>
