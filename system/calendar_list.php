<?php
	$modid=1;
  require("Connections/freedomrising.php");
?><?php
   $currentevents=$dbcon->Execute("SELECT *  FROM events  WHERE date >Now()-1 ORDER BY date desc") or DIE($dbcon->ErrorMsg());
   $currentevents_numRows=0;
   $currentevents__totalRows=$currentevents->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $currentevents_numRows = $currentevents_numRows + $Repeat1__numRows;
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
<h2>Current Events</h2>
<p><a href="calendar_oldlist.php">View Old Events</a></p>
<?php while (($Repeat1__numRows-- != 0) && (!$currentevents->EOF)) 
   { 
?>
<table width="90%" border="0" cellspacing="1" cellpadding="0" align="center">
  <tr> 
    <td><b>Event</b></td>
    <td><b>Start Date</b></td>
    <td><b>ID</b></td>
    <td>&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC"> 
    <td> 
      <?php echo $currentevents->Fields("event")?>
    </td>
    <td> 
      <?php echo $currentevents->Fields("date") ?>
    </td>
    <td> 
      <?php echo $currentevents->Fields("id")?>
    </td>
    <td><A HREF="calendar_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$currentevents->Fields("id") ?>">edit</A></td>
  </tr>
</table>
<?php
  $Repeat1__index++;
  $currentevents->MoveNext();
}
?><?php
  $currentevents->Close();
?><?php include("footer.php"); ?>
