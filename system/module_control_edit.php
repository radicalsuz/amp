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
// *** Update Record: set Variables
 
    if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "module_control";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "module_control_list.php?modid=$modid";
    $MM_fieldsStr =
"modid|value|var|value|display|value|description|value|setting|value";
    $MM_columnsStr = "modid|',none,''|var|',none,''|display|',none,''|description|',none,''|setting|',none,''";
	
    require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }

$Recordset1__MMColParam = "900000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}

   $Recordset1=$dbcon->Execute("SELECT * FROM module_control WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?>
<?php
   $templatelab=$dbcon->Execute("SELECT id, name FROM modules") or DIE($dbcon->ErrorMsg());
   $templatelab_numRows=0;
   $templatelab__totalRows=$templatelab->RecordCount();
?>
<?php include ("header.php");?>
<h2><?php echo helpme(""); ?>Edit Module Control</h2>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>">
             
              
        <table width="90%" border="0">
          <tr> 
            <td class="name">Control Name</td>
            <td> <input name="description" type="text" id="description"  size="50" value="<?php echo $Recordset1->Fields("description")?>">
            </td>
          </tr>
          <tr> 
            <td class="name">Module</td>
            <td><select name="modid" id="modid">
                
                <?php
  if ($templatelab__totalRows > 0){
    $templatelab__index=0;
    $templatelab->MoveFirst();
    WHILE ($templatelab__index < $templatelab__totalRows){
?>
                <option value="<?php echo  $templatelab->Fields("id")?>"<?php if ($templatelab->Fields("id")==$Recordset1->Fields("modid")) echo "SELECTED";?>> 
                <?php echo  $templatelab->Fields("name");?> </option>
                <?php
      $templatelab->MoveNext();
      $templatelab__index++;
    }
    $templatelab__index=0;  
    $templatelab->MoveFirst();
  }
?>
              </select></td>
          </tr>
          <tr> 
            <td class="name">Var name</td>
            <td><input name="var" type="text" id="var"  size="50" value="<?php echo $Recordset1->Fields("var")?>"></td>
          </tr>
          <tr> 
            <td valign="top" class="name">Value</td>
            <td><textarea name="setting" cols="50" rows="10" wrap="VIRTUAL" id="setting"><?php echo $Recordset1->Fields("setting")?></textarea>
              <input type="hidden" name="display" value="1"></td>
          </tr>
        </table>
  <p> 
            <input type="submit" name="Submit" value="Submit">
          <input type="submit" name="MM_delete" value="Delete Record">
       
   <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
        <input type="hidden" name="MM_insert" value="true">
        <?php 
		}
		else { ?>
        <input type="hidden" name="MM_update" value="true">
        <?php } ?>
        <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
      </form>
<?php include ("footer.php");?>
