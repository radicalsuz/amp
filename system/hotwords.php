<?php
$modid=19;

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");

$obj = new SysMenu; 

$formtitle = "HotWord";
$named = "hotwords";
$tablein = "hotwords";
$filename = "hotwords.php";
 // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();

 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
 
    $MM_editTable  = $tablein;
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = $filename;
    $MM_fieldsStr = "word|value|url|value|section|value|publish|value";
    $MM_columnsStr = "word|',none,''|url|',none,''|section|',none,1|publish|none,1,0";
  
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }

$Recordset2__MMColParam = "900000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset2__MMColParam = $HTTP_GET_VARS["id"];}
   $all=$dbcon->Execute("SELECT *  FROM $tablein order by word") or DIE($dbcon->ErrorMsg());


   $called=$dbcon->Execute("SELECT * FROM $tablein WHERE id = " . ($Recordset2__MMColParam) . "") or DIE($dbcon->ErrorMsg());

$MM_paramName = "";

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

if (isset($id)) {$typevar=$all->Fields("section");}
else {$typevar=1;}
   $typelab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$typevar."") or DIE($dbcon->ErrorMsg());

include ("header.php");?>

      <h2><?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "Add";} else {echo "Edit";} ?> <?php echo $formtitle ;?></h2>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>" >
        <table width="90%" border="0" align="center">
          <tr> 
            <td>Word</td>
            <td> <input type="text" name="word" size="50" value="<?php echo $called->Fields("word")?>"> 
            </td>
          </tr>
          <tr> 
            <td>URL</td>
            <td><input name="url" type="text" value="<?php echo $called->Fields("url")?>" size="50"></td>
          </tr>
          <tr>
            <td>Sectional Word: </td>
            <td><select name="section">
	  <OPTION VALUE="<?php echo  $typelab->Fields("id")?>" SELECTED><?php echo  $typelab->Fields("type")?></option>
	  
	  
	  <?php echo $obj->select_type_tree(0); ?></Select></td>
          </tr>
          <tr>
            <td>Publish</td>
            <td><input name="publish" type="checkbox" id="publish" value="1" <?php if ($called->Fields("publish") == 1) {echo "checked";}?>></td>
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
          <td>Word</td>
          <td>url</td>
          <td>used</td>
          <td>&nbsp;</td>
        </tr>
        <?php while (!$all->EOF)
   { 

?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $all->Fields("word")?> </td>
          <td>  <?php echo $all->Fields("url")?></td>
          <td><?php if ($all->Fields("publish") ==1 ) {echo "yes";} else {echo "no";}?></td>
          <td><A HREF="<?php echo $filename;?>?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$all->Fields("id") ?>">edit</A></td>
        </tr>
        <?php
  
  $all->MoveNext();
}
?>
      </table>
<a href="<?php echo $filename;?>">Add A <?php echo $formtitle ;?></a>
  

<?php

include("footer.php");

?>
