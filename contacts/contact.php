<?php
     
  
  require_once("Connections/freedomrising.php");  

?>
<?php
$calledrcd__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$calledrcd__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
$enteredby__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$enteredby__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
$modifiedby__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$modifiedby__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
   $calledrcd=$dbcon->Execute("SELECT * FROM contacts2 WHERE id = " . ($calledrcd__MMColParam) . "") or DIE($dbcon->ErrorMsg());

   $allclass=$dbcon->Execute("SELECT contacts_class.title FROM contacts2 Inner Join contacts_class on contacts2.classid=contacts_class.id WHERE contacts2.id = " . ($modifiedby__MMColParam) . "") or DIE($dbcon->ErrorMsg());

   $enteredby=$dbcon->Execute("SELECT users.name  FROM contacts2 Inner Join users on contacts2.enteredby=users.id  WHERE contacts2.id = " . ($enteredby__MMColParam) . "") or DIE($dbcon->ErrorMsg());

   $source=$dbcon->Execute("SELECT source.title  FROM contacts2 Inner Join source on contacts2.source=source.id  WHERE contacts2.id = " . ($enteredby__MMColParam) . "") or DIE($dbcon->ErrorMsg());

   $modifiedby=$dbcon->Execute("SELECT users.name  FROM contacts2 Inner Join users on contacts2.modifiedby=users.id  WHERE contacts2.id = " . ($modifiedby__MMColParam) . "") or DIE($dbcon->ErrorMsg());

    $camps=$dbcon->Execute("SELECT id, name from contacts_campaign") or DIE($dbcon->ErrorMsg());
   $camps_numRows=0;
   $camps__totalRows=$camps->RecordCount();

   $region=$dbcon->Execute("SELECT region.title FROM contacts2 Inner Join region on contacts2.regionid=region.id WHERE contacts2.id = " . ($modifiedby__MMColParam) . "") or DIE($dbcon->ErrorMsg());



   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $camps_numRows = $camps_numRows + $Repeat1__numRows;

?><?php
  // *** Recordset Stats, Move To Record, and Go To Record: declare stats variables
  
  // set the record count
  $calledrcd_total = $calledrcd->RecordCount();
  
  // set the number of rows displayed on this page
  if ($calledrcd_numRows < 0) {            // if repeat region set to all records
    $calledrcd_numRows = $calledrcd_total;
  } else if ($calledrcd_numRows == 0) {    // if no repeat regions
    $calledrcd_numRows = 1;
  }
  
  // set the first and last displayed record
  $calledrcd_first = 1;
  $calledrcd_last  = $calledrcd_first + $calledrcd_numRows - 1;
  
  // if we have the correct record count, check the other stats
  if ($calledrcd_total != -1) {
    $calledrcd_numRows = min($calledrcd_numRows, $calledrcd_total);
    $calledrcd_first  = min($calledrcd_first, $calledrcd_total);
    $calledrcd_last  = min($calledrcd_last, $calledrcd_total);
  }
  ?><?php $MM_paramName = ""; ?><?php
// *** Move To Record and Go To Record: declare variables

$MM_rs	  = &$calledrcd;
$MM_rsCount   = $calledrcd_total;
$MM_size      = $calledrcd_numRows;
$MM_uniqueCol = "id";
$MM_paramName = "id";
$MM_offset = 0;
$MM_atTotal = false;
$MM_paramIsDefined = ($MM_paramName != "" && isset($$MM_paramName));
?><?php
// *** Move To Record: handle 'index' or 'offset' parameter

if (!$MM_paramIsDefined && $MM_rsCount != 0) {

	// use index parameter if defined, otherwise use offset parameter
	if(isset($index)){
		$r = $index;
	} else {
		if(isset($offset)) {
			$r = $offset;
		} else {
			$r = 0;
		}
	}
	$MM_offset = $r;

	// if we have a record count, check if we are past the end of the recordset
	if ($MM_rsCount != -1) {
		if ($MM_offset >= $MM_rsCount || $MM_offset == -1) {  // past end or move last
			if (($MM_rsCount % $MM_size) != 0) {  // last page not a full repeat region
				$MM_offset = $MM_rsCount - ($MM_rsCount % $MM_size);
			}
			else {
				$MM_offset = $MM_rsCount - $MM_size;
			}
		}
	}

	// move the cursor to the selected record
	for ($i=0;!$MM_rs->EOF && ($i < $MM_offset || $MM_offset == -1); $i++) {
		$MM_rs->MoveNext();
	}
	if ($MM_rs->EOF) $MM_offset = $i;  // set MM_offset to the last possible record
}
?><?php
// *** Move To Record: if we dont know the record count, check the display range

if ($MM_rsCount == -1) {

  // walk to the end of the display range for this page
  for ($i=$MM_offset; !$MM_rs->EOF && ($MM_size < 0 || $i < $MM_offset + $MM_size); $i++) {
    $MM_rs->MoveNext();
  }

  // if we walked off the end of the recordset, set MM_rsCount and MM_size
  if ($MM_rs->EOF) {
    $MM_rsCount = $i;
    if ($MM_size < 0 || $MM_size > $MM_rsCount) $MM_size = $MM_rsCount;
  }

  // if we walked off the end, set the offset based on page size
  if ($MM_rs->EOF && !$MM_paramIsDefined) {
    if (($MM_rsCount % $MM_size) != 0) {  // last page not a full repeat region
      $MM_offset = $MM_rsCount - ($MM_rsCount % $MM_size);
    } else {
      $MM_offset = $MM_rsCount - $MM_size;
    }
  }

  // reset the cursor to the beginning
  $MM_rs->MoveFirst();

  // move the cursor to the selected record
  for ($i=0; !$MM_rs->EOF && $i < $MM_offset; $i++) {
    $MM_rs->MoveNext();
  }
}
?><?php
// *** Move To Record: update recordset stats

// set the first and last displayed record
$calledrcd_first = $MM_offset + 1;
$calledrcd_last  = $MM_offset + $MM_size;
if ($MM_rsCount != -1) {
  $calledrcd_first = $calledrcd_first<$MM_rsCount?$calledrcd_first:$MM_rsCount;
  $calledrcd_last  = $calledrcd_last<$MM_rsCount?$calledrcd_last:$MM_rsCount;
}

// set the boolean used by hide region to check if we are on the last record
$MM_atTotal = ($MM_rsCount != -1 && $MM_offset + $MM_size >= $MM_rsCount);
?><?php
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
?><?php
// *** Move To Record: set the strings for the first, last, next, and previous links

$MM_moveFirst="";
$MM_moveLast="";
$MM_moveNext="";
$MM_movePrev="";
$MM_keepMove = $MM_keepBoth;  // keep both Form and URL parameters for moves
$MM_moveParam = "index";

// if the page has a repeated region, remove 'offset' from the maintained parameters
if ($MM_size > 1) {
  $MM_moveParam = "offset";
  if (strlen($MM_keepMove)> 0) {
    $params = explode("&", $MM_keepMove);
    $MM_keepMove = "";
    for ($i=0; $i < sizeof($params); $i++) {
      list($nextItem) = explode("=", $params[$i]);
      if (strtolower($nextItem) != $MM_moveParam)  {
        $MM_keepMove.="&".$params[$i];
      }
    }
    if (strlen($MM_keepMove) > 0) $MM_keepMove = substr($MM_keepMove, 1);
  }
}

// set the strings for the move to links
if (strlen($MM_keepMove) > 0) $MM_keepMove.="&";
$urlStr = $PHP_SELF."?".$MM_keepMove.$MM_moveParam."=";
$MM_moveFirst = $urlStr."0";
$MM_moveLast  = $urlStr."-1";
$MM_moveNext  = $urlStr.($MM_offset + $MM_size);
$MM_movePrev  = $urlStr.(max($MM_offset - $MM_size,0));
?><?php include ("header.php");
?>
<link rel="stylesheet" href="site.css" type="text/css"></head>
<table width="90%" border="0" cellspacing="3" cellpadding="0">
  <tr class="toplinks">
    <td colspan="4">Personal Information</td>
  </tr>
  <tr>
    <td width="14%" class="title">Name</td>
    <td width="35%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $calledrcd->Fields("Suffix")?>&nbsp;<?php echo $calledrcd->Fields("FirstName")?>&nbsp;<?php echo $calledrcd->Fields("LastName")?></td>
    <td width="15%" class="title">Campus</td>
    <td width="36%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo  $calledrcd->Fields("campus");?></td>
  </tr>
  <tr>
    <td class="title">Organization </td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $calledrcd->Fields("Company")?></td>
    <td class="title">Type</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo  $allclass->Fields("title");?></td>
  </tr>
  <tr>
    <td class="title">Position</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $calledrcd->Fields("JobTitle")?></td>
    <td class="title">Home Phone</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $calledrcd->Fields("HomePhone")?></td>
  </tr>
  <tr>
    <td class="title">Work Phone</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $calledrcd->Fields("BusinessPhone")?></td>
    <td class="title">Fax Phone</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $calledrcd->Fields("BusinessFax")?></td>
  </tr>
  <tr>
    <td class="title">Mobile Phone</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $calledrcd->Fields("MobilePhone")?></td>
    <td class="title">Entered By</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $enteredby->Fields("name");?></td>
  </tr>
  <tr>
    <td class="title">E-Mail</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:<?php echo $calledrcd->Fields("EmailAddress")?>"><?php echo $calledrcd->Fields("EmailAddress")?></a></td>
    <td class="title">Modified Last By</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $modifiedby->Fields("name");?></td>
  </tr>
  <tr>
    <td class="title">E-Mail 2</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:<?php echo $calledrcd->Fields("EmailAddress")?>"><?php echo $calledrcd->Fields("Email2Address")?></a></td>
    <td class="title">Source</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $source->Fields("title");?></td>
  </tr>
  <tr>
    <td class="title">Web Page</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://<?php echo $calledrcd->Fields("WebPage")?>"><?php echo $calledrcd->Fields("WebPage")?></a></td>
    <td class="title">Region</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $region->Fields("title");?></td>
  </tr>
  <tr>
    <td class="title">Notes </td>
    <td colspan="3"><?php echo $calledrcd->Fields("notes")?></td>
  </tr>
  <tr class="toplinks">
    <td colspan="2">&nbsp;&nbsp;Work Address</td>
    <td colspan="2">&nbsp;&nbsp;Home Address</td>
  </tr>
  <td>&nbsp;</td>
      <td colspan="2" valign="top"><?php echo $calledrcd->Fields("BusinessStreet")?>
          <?php if ($calledrcd->Fields("BusinessStreet2") != ($null)) { ?>
          <br>
          <?php echo $calledrcd->Fields("BusinessStreet2")?>
          <?php } ?>
          <br>
          <?php echo $calledrcd->Fields("BusinessCity")?>&nbsp;<?php echo $calledrcd->Fields("BusinessState")?>&nbsp;<?php echo $calledrcd->Fields("BusinessPostalCode")?> <br>
          <?php echo $calledrcd->Fields("BusinessCountry")?> <br>
      </td>
      <td valign="top"><?php echo $calledrcd->Fields("HomeStreet")?>
          <?php if ($calledrcd->Fields("HomeStreet2") != ($null)) { ?>
          <br>
          <?php echo $calledrcd->Fields("HomeStreet2")?>
          <?php } ?>
          <br>
          <?php echo $calledrcd->Fields("HomeCity")?>&nbsp;<?php echo $calledrcd->Fields("HomeState")?>&nbsp;<?php echo $calledrcd->Fields("HomePostalCode")?> <br>
          <?php echo $calledrcd->Fields("HomeCountry")?> </td>
  </tr>
 
  <?php
  $custom=$dbcon->Execute("select v.value, f.name, c.name as campaign from contacts_rel v, contacts_campaign c, contacts_fields f  where v.fieldid =f.id and f.camid= c.id and v.value !='' and v.perid = $_GET[id] order by c.fieldorder, f.fieldorder ") or DIE($dbcon->ErrorMsg());
  $selected = '';
  while  (!$custom->EOF) { 
 if ($custom->Fields("campaign") != $selected) echo ' <tr class="toplinks"><td colspan="4">'. $custom->Fields("campaign") .'</td></tr>';
 echo '<tr><td class="title">'.$custom->Fields("name").'</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$custom->Fields("value").'</td></tr>';
  $selected = $custom->Fields("campaign");
  $custom->MoveNext();}
  ?>
 
</table>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>">
 <?php if ($userper[95] == 1 or $standalone == 1){{} ?> <p align="center"><strong><a href="contact_edit.php?id=<?php echo $calledrcd->Fields("id") ?>">EDIT 
    THIS RECORD</a></strong></p><?php if ($userper[95] == 1 or $standalone == 1){}} ?>

  <input type="hidden" name="MM_update" value="true">

  <input type="hidden" name="MM_recordId" value="<?php echo $calledrcd->Fields("id") ?>">
</form>
    
</body>
</html>
<?php
  $calledrcd->Close();
 
  $allclass->Close();
  $enteredby->Close();
  $region->Close();
  $modifiedby->Close();
  $source->Close();
  $camps->Close();
?>
<?php include ("footer.php");?>
