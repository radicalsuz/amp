<?php
/*********************
05-06-2003  v3.01
Module:  Housing
Description:  housing confirmation page  
GET VARS step
To Do:  
				
*********************/ 
$modid = 3;
$mod_id = 19;
include("sysfiles.php");
include("header.php"); 
include("dropdown.php"); 

  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";

  if (isset($MM_update) && (isset($MM_recordId))) {

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

?>


 <?php if ($HTTP_GET_VARS["step"] == (admin)){; ?>

Your posting has been added to the board.  The moderator will appove yout posting soon.<?php } ?>

 <?php if ($HTTP_GET_VARS["step"] == (email)){; ?>

An e-mail has been sent to your e-mail account with insturctions on how to confirm your posting.<?php } ?>

 <?php if ($HTTP_GET_VARS["step"] == ($null)){; ?>

<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form1">

  <?php echo $called->Fields("firstname")?>

  &nbsp; 

  <?php echo $called->Fields("lastname")?>

  &nbsp; 

  <?php echo $called->Fields("email")?>

  <input type="submit" name="Submit" value="Add Posting">

  <input type="hidden" name="publish" value="1">

  <input type="hidden" name="MM_update" value="true">

  <input type="hidden" name="MM_recordId" value="<?php echo $called->Fields("id") ?>">

</form><?php } ?>

<?php
  $called->Close();
?>

<?php include("footer.php"); ?>