<?php
$modid=4;
  require("Connections/freedomrising.php");
?>
<?php
   $faq=$dbcon->Execute("SELECT * FROM faq ORDER BY date DESC") or DIE($dbcon->ErrorMsg());
   $faq_numRows=0;
   $faq__totalRows=$faq->RecordCount();
?>
<?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $faq_numRows = $faq_numRows + $Repeat1__numRows;
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
<?php include("header.php"); ?>
<h2>FAQs</h2>
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td><b>question</b></td>
    <td><b>id</b></td>
    <td><b>publish</b></td>
    <td><b>answered</b></td>
    <td>&nbsp;</td>
  </tr>
  <?php while (($Repeat1__numRows-- != 0) && (!$faq->EOF)) 
   { 
?>
  <tr bgcolor="#CCCCCC"> 
    <td> 
      <?php echo $faq->Fields("question")?>
    </td>
    <td> 
      <?php echo $faq->Fields("id")?>
    </td>
    <td> 
      <input <?php If (($faq->Fields("publish")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox" value="1">
    </td>
    <td> 
      <input <?php If (($faq->Fields("answered")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox2" value="1">
    </td>
    <td><A HREF="faq_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$faq->Fields("id") ?>">edit</A></td>
  </tr>
  <?php
  $Repeat1__index++;
  $faq->MoveNext();
}
?>
</table>
<?php include("footer.php"); ?>
<?php
  $faq->Close();
?>
