<?php
     
  
  require_once("Connections/freedomrising.php");  

?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
     ob_start();

// *** Insert Record: set Variables

if (isset($MM_insert)){

   // $MM_editConnection = MM_freedomrising_STRING;
   $MM_editTable  = "contacts_class";
   $MM_editRedirectUrl = "admin.php";
   $MM_fieldsStr = "name|value|password|value";
   $MM_columnsStr = "title|',none,''|description|',none,''";
 
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

 if (isset($MM_update) && (isset($MM_recordId))) {
  
//    $MM_editConnection = $MM_freedomrising_STRING;
       $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
   	   $MM_editTable  = "contacts_class";
   $MM_editRedirectUrl = "admin.php";
   $MM_fieldsStr = "name|value|password|value";
   $MM_columnsStr = "title|',none,''|description|',none,''";
    
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
// *** Delete Record: declare variables
  if (isset($MM_delete) && (isset($MM_recordId))) {
//    $MM_editConnection = $MM_freedomrising_STRING;
    $MM_editTable  = "contacts_class";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "admin.php";
  
    if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
      $MM_editRedirectUrl = $MM_editRedirectUrl . ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
require ("../Connections/dataactions.php");
ob_end_flush();


?><?php
$Recordset1__MMColParam = "8000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
  
   $Recordset1=$dbcon->Execute("SELECT id, title, description FROM contacts_class WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php include ("header.php"); ?>

<h2>Types</h2>
<form method="post" action="<?php echo $MM_editAction?>" name="form1">
  <table border=0 cellpadding=2 cellspacing=0 align="center">
    <tr valign="baseline"> 
      <td nowrap align="right">Name:</td>
      <td> 
        <input type="text" name="name" value="<?php echo $Recordset1->Fields("title")?>" size="40">
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Description:</td>
      <td> 
        <textarea name="password" cols="40" rows="4" wrap="VIRTUAL"><?php echo $Recordset1->Fields("description")?></textarea>
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">&nbsp;</td>
      <td>&nbsp; </td>
    </tr>
  </table>
  <?php if ($Recordset1->Fields("id") == ($null)){?>
  <input type="hidden" name="MM_insert" value="true">
  <?php
}
 if ($Recordset1->Fields("id") != ($null)){?>
 <input type="hidden" name="MM_update" value="true">
 <input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>"><?php } ?>
 <input type="submit" name="Submit" value="Update">
 </form>
 
 <form name="delete" method="POST" action="<?php echo $MM_editAction?>">
 <input type="hidden" name="MM_delete" value="true">
 <input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>">
 <input type="submit" name="Submit2" value="Delete"></form>
<p>&nbsp;</p>

<?php
  $Recordset1->Close();
?>
<?php include ("footer.php");?>
