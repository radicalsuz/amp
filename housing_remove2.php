<?php
/*********************
05-06-2003  v3.01
Module:  Housing
Description:  2nd stage of  housing remove 
To Do:  declare POST vars
				
*********************/ 

$modid = 3;
$mod_id = 19;
include("sysfiles.php");
include("header.php"); 
include("dropdown.php"); 
?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
?><?php
  // *** Update Record: set variables
  
  if (isset($MM_update) && (isset($MM_recordId))) {
  
//    $MM_editConnection = $MM_freedomrising_STRING;
    $MM_editTable  = "housing";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "housing.php";
    $MM_fieldsStr = "publish|value";
    $MM_columnsStr = "publish|none,none,NULL";
     require ("Connections/insetstuff.php"); 
require ("Connections/dataactions.php"); }

$called__MMColParam = "1";
if (isset($HTTP_GET_VARS["email"]))
  {$called__MMColParam = $HTTP_GET_VARS["email"];}

   $called=$dbcon->Execute("SELECT *  FROM housing  WHERE email = '" . ($called__MMColParam) . "'") or DIE($dbcon->ErrorMsg());
   $called_numRows=0;
   $called__totalRows=$called->RecordCount();
?>

<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form1">
  <?php echo $called->Fields("firstname")?>
  &nbsp; 
  <?php echo $called->Fields("lastname")?>
  &nbsp; 
  <?php echo $called->Fields("email")?>
  <input type="submit" name="Submit" value="Remove">
  <input type="hidden" name="publish" value="0">
  <input type="hidden" name="MM_update" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $called->Fields("id") ?>">
</form>

<?php
  $called->Close();
?>
<?php include("footer.php"); ?>