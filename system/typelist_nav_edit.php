<?php
  require("Connections/freedomrising.php");
?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();
?><?php
  // *** Update Record: set variables
  
  if (isset($MM_update) && (isset($MM_recordId))) {
   //Delete cached versions of output file
   
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "nav";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "typelist_nav_edit.php";
    $MM_fieldsStr = "navid|value|position|value|moduleid|value|id|value";
    $MM_columnsStr = "navid|none,none,NULL|position|',none,''|typelist|none,none,NULL|id|none,none,NULL";
  
  require ("../Connections/insetstuff.php");
  }
  ?><?php
  // *** Delete Record: declare variables
  if (isset($MM_delete) && (isset($MM_recordId))) {
   //Delete cached versions of output file
  //    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "nav";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "typelist_nav_edit.php";
  
    if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
      $MM_editRedirectUrl = $MM_editRedirectUrl . ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
require ("../Connections/dataactions.php");
ob_end_flush();
 
$mod_id = $HTTP_GET_VARS["id"];
 $navfiles=$dbcon->Execute("SELECT * FROM nav WHERE typelist=$id order by position asc") or DIE($dbcon->ErrorMsg());
    $navfiles_numRows=0;
   $navfiles__totalRows=$navfiles->RecordCount();
    $allnav=$dbcon->Execute("SELECT modules.name as mod, navtbl.name, navtbl.id FROM navtbl, modules where modules.id= navtbl.modid order by modules.name asc, navtbl.name asc") or DIE($dbcon->ErrorMsg());
   $allnav_numRows=0;
   $allnav__totalRows=$allnav->RecordCount();
   
   $Repeat2__numRows = -1;
   $Repeat2__index= 0;
   $navfiles_numRows = $navfiles_numRows + $Repeat2__numRows;

   $Repeat3__numRows = -1;
   $Repeat3__index= 0;
   $allnav_numRows = $allnav_numRows + $Repeat3__numRows;?>
   <?php include ("header.php"); ?>
<h2>Navagition Order Selection</h2>

<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
   <tr class="intitle"> 
    <td>Update</td>
              <td>Naviagtion File</td>
              <td>Position</td>
			  <td>Delete</td>
  </tr>
  <?php    while (($Repeat2__numRows-- != 0) && (!$navfiles->EOF)) 
   { ?>
  <tr>
    <td><form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="Form1">
	
	<input type="submit" name="Submit" value="Update Record">&nbsp;</td>
    <td>
	<select name="navid">
                  <?php
  if ($allnav__totalRows > 0){
    $allnav__index=0;
    $allnav->MoveFirst();
    WHILE ($allnav__index < $allnav__totalRows){
?>
                  <OPTION VALUE="<?php echo  $allnav->Fields("id")?>"<?php if ($allnav->Fields("id")==$navfiles->Fields("navid")) echo "SELECTED";?>> 
                  <?php echo  $allnav->Fields("mod");?>- <?php echo  $allnav->Fields("name");?> </OPTION>
                   <?php
      $allnav->MoveNext();
      $allnav__index++;
    }
    $allnav__index=0;  
    $allnav->MoveFirst();
  }
?>
                </select>
	
	
	</td>
    <td><select name="position">
					<option value="<?php echo $navfiles->Fields("position");?>" selected><?php echo $navfiles->Fields("position"); ?></option>
                  <option value="L1">L1</option>
                  <option value="L2">L2</option>
                  <option value="L3">L3</option>
                  <option value="L4">L4</option>
                  <option value="L5">L5</option>
                  <option value="L6">L6</option>
                  <option value="L7">L7</option>
                  <option value="L8">L8</option>
                  <option value="L9">L9</option>
                  <option value="R1">R1</option>
                  <option value="R2">R2</option>
                  <option value="R3">R3</option>
                  <option value="R4">R4</option>
                  <option value="R5">R5</option>
                  <option value="R6">R6</option>
                  <option value="R7">R7</option>
                  <option value="R8">R8</option>
                  <option value="R9">R9</option>
                </select>
    <input type="hidden" name="moduleid" value="<?php echo $navfiles->Fields("typelist"); ?>">
	<input type="hidden" name="id" value="<?php echo $navfiles->Fields("id"); ?>">
  
  <input type="hidden" name="MM_update" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $navfiles->Fields("id"); ?>">
</form>
				</td>
		<td><form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="submit" name="Delete" value="Delete">
  <input type="hidden" name="MM_delete" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $navfiles->Fields("id") ?>">
</form></td>
  </tr>
  	<?php $Repeat2__index++;
 	$navfiles->MoveNext();
}
 $navfiles->Close();
		?>
  
</table>
<a href="typelist_nav_add.php?id=<?php echo $HTTP_GET_VARS["id"]; ?>"> Insert Another Navigation File</a><br>
<a href="type_edit.php?id=<?php echo $HTTP_GET_VARS["id"]; ?>">Back to Type</a>
	
		
		
<?php include("footer.php"); ?>