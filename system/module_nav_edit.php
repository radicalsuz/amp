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
  
?><?php
  // *** Update Record: set variables
  
  if (isset($MM_update) && (isset($MM_recordId))) {
   //Delete cached versions of output file
   
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "nav";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "module_nav_edit.php";
    $MM_fieldsStr = "navid|value|position|value|moduleid|value|id|value";
    $MM_columnsStr = "navid|none,none,NULL|position|',none,''|moduleid|none,none,NULL|id|none,none,NULL";
  
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
    $MM_editRedirectUrl = "module_nav_edit.php";
  
    if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
      $MM_editRedirectUrl = $MM_editRedirectUrl . ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
require ("../Connections/dataactions.php");
ob_end_flush();
 
$mod_id = $HTTP_GET_VARS["id"];
 $navfiles=$dbcon->Execute("SELECT * FROM nav WHERE moduleid=$mod_id order by position asc") or DIE($dbcon->ErrorMsg());
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


<table width="98%" border="0" align="center" >
  <tr class="banner"> 
    <td colspan="4"><?php echo helpme("nav"); ?>Edit<?php if ($id ==2) {echo " Home Page";}?> Components</td>
  </tr>
  
  <tr class="intitle"> 
    <td></td>
    <td>Navigation File</td>
    <td>Position</td>
    <td><div align="right"></div></td>
  </tr>
  <?php    while (($Repeat2__numRows-- != 0) && (!$navfiles->EOF)) 
   { ?>
  <tr> <td valign="top">
    <form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="Form1">
      <input type="submit" name="Submit" value="Update">
      &nbsp;</td>
      <td valign="top"> <select name="navid">
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
        </select> </td>
      <td valign="top"><select name="position">
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
        </select> <input type="hidden" name="moduleid" value="<?php echo $navfiles->Fields("moduleid"); ?>"> 
        <input type="hidden" name="id" value="<?php echo $navfiles->Fields("id"); ?>"> 
        <input type="hidden" name="MM_update" value="true"> <input type="hidden" name="MM_recordId" value="<?php echo $navfiles->Fields("id"); ?>"> 
    </form></td>
    <td valign="top"><form name="delete" method="POST" action="<?php echo $MM_editAction?>">
        <div align="right"> 
          <input type="submit" name="Delete" value="Delete">
          <input type="hidden" name="MM_delete" value="true">
          <input type="hidden" name="MM_recordId" value="<?php echo $navfiles->Fields("id") ?>">
        </div>
      </form></td>
  </tr>
 
  <?php $Repeat2__index++;
 	$navfiles->MoveNext();
}
 $navfiles->Close();
		?>
		 <tr > 
    <td colspan="4"><font size="3" face="Verdana, Arial, Helvetica, sans-serif"><strong><a href="module_nav_add.php?id=<?php echo $HTTP_GET_VARS["id"]; ?>"> 
Insert Another Component</a></strong></font><br>
<?php if ($id= 2) { ?>
<strong><a href="nav_minedit.php?goto=1"><font size="3" face="Verdana, Arial, Helvetica, sans-serif">Add 
Navigation Component</font></a><br>
</strong> <strong><a href="nav_list.php?nons=1"><font size="3" face="Verdana, Arial, Helvetica, sans-serif">View/ 
Edit Navigation Component</font></a> </strong> 
<?php 
}
else { ?>
<strong><a href="moduletext_edit.php?id=<?php echo $HTTP_GET_VARS["id"]; ?>"><font size="3" face="Verdana, Arial, Helvetica, sans-serif">Back 
to Module</font></a></strong> </font> 
<?php }?></td>
  </tr>
</table>
<br>

<?php include("footer.php"); ?>