<?php

$modid=11;

require("Connections/freedomrising.php");

// *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();

  // *** Update Record: set variables
  
  if (isset($MM_update) && (isset($MM_recordId))) {
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "linktype";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "linktype_list.php";
    $MM_fieldsStr = "name|value|publish|value";
    $MM_columnsStr = "name|',none,''|publish|none,1,0";
  
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
  
  if (isset($MM_insert)){

   // $MM_editConnection = MM__STRING;
   $MM_editTable  = "linktype";
   $MM_editRedirectUrl = "linktype_list.php";
   $MM_fieldsStr = "name|value";
   $MM_columnsStr = "name|',none,''";

  // create the $MM_fields and $MM_columns arrays
   $MM_fields = explode("|", $MM_fieldsStr);
   $MM_columns = explode("|", $MM_columnsStr);
  
  // set the form values
  for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    $MM_fields[$i+1] = $$MM_fields[$i];
 	}
 }
 
 // *** Delete Record: declare variables
 if (isset($MM_delete) && (isset($MM_recordId))) {
	//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "linktype";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "linktype_list.php";
  
    if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
      $MM_editRedirectUrl = $MM_editRedirectUrl . ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
  require ("../Connections/dataactions.php");
  ob_end_flush();

$fields = array();

if (isset($_REQUEST["id"])) {
	$linkTypeRs = $dbcon->Execute("SELECT * FROM linktype WHERE id = " . $dbcon->qstr($_REQUEST['id']))
		or DIE($dbcon->ErrorMsg());

	$fields['id']      = $linkTypeRs->Fields('id');
	$fields['name']    = $linkTypeRs->Fields('name');
	$fields['publish'] = $linkTypeRs->Fields('publish');
}

include("header.php"); ?>

<h2>Edit Link Type</h2>
<form method="POST" action="<?php echo $MM_editAction?>" name="form1">
  <table border=0 cellpadding=2 cellspacing=0 align="center">
    <tr valign="baseline"> 
      <td nowrap align="right">Name:</td>
      <td> 
        <input type="text" name="name" value="<?= $fields['name'] ?>" size="32">
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Publish:</td>
      <td> 
        <input type="checkbox" name="publish" value="1" size="32" <?= ($fields['publish']==1) ? "CHECKED" : '' ?>>
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">&nbsp;</td>
      <td> 
        <input type="submit" value="Update Record" name="submit">
      </td>
    </tr>
  </table>
<?php 
  
    if (!isset($_REQUEST['id'])) {
		echo '<input type="hidden" name="MM_insert" value="true">';
	} else {
       	echo '<input type="hidden" name="MM_update" value="true">';
	}
	
?>

  <input type="hidden" name="MM_recordId" value="<?= $fields['id'] ?>">
</form>
<form name="form2" method="POST" action="<?php echo $MM_editAction?>">
  <input type="submit" name="Submit" value="delete">
  <input type="hidden" name="MM_delete" value="true">
  <input type="hidden" name="MM_recordId" value="<?= $fields["id"] ?>">
</form>

<p>&nbsp;</p>

<?php include("footer.php"); ?>