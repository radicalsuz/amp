<?php
$modid=40;
  require("Connections/freedomrising.php");
$formtitle = "Interest";
$named = "interest";
$tablein = "vol_interest";
$filename = "vol_interestedit.php";
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();
?>
<?php

 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
 
    $MM_editTable  = $tablein;
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = $filename;
    $MM_fieldsStr = "name|value|short|value|orderby|value|";
    $MM_columnsStr = "$named|',none,''|short|',none,''|orderby|',none,''";
  
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }

$Recordset2__MMColParam = "900000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset2__MMColParam = $HTTP_GET_VARS["id"];}
?>
<?php
   $all=$dbcon->Execute("SELECT *  FROM $tablein order by orderby") or DIE($dbcon->ErrorMsg());


   $called=$dbcon->Execute("SELECT * FROM $tablein WHERE id = " . ($Recordset2__MMColParam) . "") or DIE($dbcon->ErrorMsg());
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


<?php include ("header.php");?>
      <h2><?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "Add";} else {echo "Edit";} ?> <?php echo $formtitle ;?></h2>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>" >
        <table width="90%" border="0" align="center">
          <tr> 
            <td> Name</td>
            <td> <input type="text" name="name" size="50" value="<?php echo $called->Fields("$named")?>"> 
            </td>
          </tr>
          <tr> 
            <td>Short Field Name</td>
            <td><input name="short" type="text" id="short" value="<?php echo $called->Fields("short")?>" size="50"></td>
          </tr>
          <tr>
            <td>Order</td>
            <td><input name="orderby" type="text" value="<?php echo $called->Fields("orderby")?>" size="50"></td>
          </tr>
        </table>
        <p> 
          <input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes">
          <input name="MM_delete" type="submit" value="Delete Record" onClick="return confirmSubmit('Are you sure you want to DELETE this record?')"><input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">

      </form>
      <p>&nbsp;</p>
      <h2>View/Edit <?php echo $formtitle ;?></h2>
    
      <table width="90%" border="0" cellspacing="2" cellpadding="3" align="center">
        <tr class="intitle"> 
          <td>Field</td>
          <td>id</td>
          <td>Order</td>
          <td>&nbsp;</td>
        </tr>
        <?php while (!$all->EOF)
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $all->Fields("$named")?> </td>
          <td> <?php echo $all->Fields("id")?> </td>
          <td><?php echo $all->Fields("orderby")?></td>
          <td><A HREF="<?php echo $filename;?>?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$all->Fields("id") ?>">edit</A></td>
        </tr>
        <?php
  
  $all->MoveNext();
}
?>
      </table>
<a href="<?php echo $filename;?>">Add A <?php echo $formtitle ;?></a>
  

<?php
  include ("footer.php") ;?>

