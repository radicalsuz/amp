<?php
     
  
  require_once("Connections/freedomrising.php");  

?><?php
   $Recordset1=$dbcon->Execute("SELECT id, name FROM campaigns ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $Recordset1_numRows = $Recordset1_numRows + $Repeat1__numRows;
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
?><?php include ("header.php"); ?>
<h2>All Campaigns</h2>
<table width="75%" border="0" cellspacing="5" cellpadding="0" align="center">
  <tr> 
    <td class="toplinks">ID #</td>
    <td class="toplinks">Name</td>
    <td class="toplinks">edit</td>
  </tr>
  <?php while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
  <tr> 
    <td class="results"> 
      <?php echo $Recordset1->Fields("id")?>
    </td>
    <td class="title"> 
      <?php echo $Recordset1->Fields("name")?>
    </td>
    <td class="title"><A HREF="admin_camfieldsphp.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>">edit</A></td>
  </tr>
  <?php
  $Repeat1__index++;
  $Recordset1->MoveNext();
}
?>
</table>
</body>
</html>
<?php
  $Recordset1->Close();
?>
<?php include ("footer.php");?>
