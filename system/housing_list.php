<?php
$modid=3;
  require("Connections/freedomrising.php");
?><?php
   $hosuing=$dbcon->Execute("SELECT * FROM housing ORDER BY lastname ASC") or DIE($dbcon->ErrorMsg());
   $hosuing_numRows=0;
   $hosuing__totalRows=$hosuing->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $hosuing_numRows = $hosuing_numRows + $Repeat1__numRows;
?><?php $MM_paramName = ""; ?><?php
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
?><?php include("header.php"); ?>
<h2>Housing Board</h2>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr> 
    <td><b>Name</b></td>
    <td><b>email</b></td>
    <td><b>id</b></td>
    <td><b>publish</b></td>
    <td><b></b></td>
  </tr>
  <?php while (($Repeat1__numRows-- != 0) && (!$hosuing->EOF)) 
   { 
?>
  <tr> 
    <td> 
      <?php echo $hosuing->Fields("firstname")?><?php echo $hosuing->Fields("lastname")?>
    </td>
    <td> 
      <?php echo $hosuing->Fields("email")?>
    </td>
    <td> 
      <?php echo $hosuing->Fields("id")?>
    </td>
    <td> 
      <input <?php If (($hosuing->Fields("publish")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox" value="checkbox">
    </td>
    <td><A HREF="housing_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$hosuing->Fields("id") ?>">edit</A></td>
  </tr>
  <?php
  $Repeat1__index++;
  $hosuing->MoveNext();
}
?>
</table>
</body>
</html>
<?php
  $hosuing->Close();
?>
