<?php
/*********************
05-06-2003  v3.01
Module:  Housing
Description:  housing confirmation page  
GET VARS step
To Do:  
				
*********************/ 
$modid = 3;
$intro_id = 19;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  


if (isset($_POST["MM_update"]) && (isset($_POST["MM_recordId"]))) {
    $MM_editTable  = "housing";
    $MM_editColumn = "id";
    $MM_recordId = intval( $_POST["MM_recordId"] );
    $MM_editRedirectUrl = "housing.php";
    $MM_fieldsStr = "publish|value";
    $MM_columnsStr = "publish|none,none,NULL";
	require ("DBConnections/insetstuff.php");
    require ("DBConnections/dataactions.php");
}

$called__MMColParam = "1";
if (isset($_GET["email"])){
	$called__MMColParam = $_GET["email"];
}
$called=$dbcon->Execute("SELECT *  FROM housing  WHERE email = " . $dbcon->qstr($called__MMColParam) );

if ($_GET["step"] == "admin"){
	echo "Your posting has been added to the board.  The moderator will appove yout posting soon."
} 
if ($_GET["step"] == "email"){
	echo "An e-mail has been sent to your e-mail account with insturctions on how to confirm your posting."
}

if ( $called && $_GET["step"] == NULL){ ?>

<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form1">

<?php 
echo $called->Fields("firstname").'&nbsp;'.echo $called->Fields("lastname").'&nbsp;'.$called->Fields("email");
?>

  <input type="submit" name="Submit" value="Add Posting">

  <input type="hidden" name="publish" value="1">

  <input type="hidden" name="MM_update" value="true">

  <input type="hidden" name="MM_recordId" value="<?php echo $called->Fields("id") ?>">

</form><?php 
} 
include("AMP/BaseFooter.php"); 
?>
