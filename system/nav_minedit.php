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
    $MM_editTable  = "navtbl";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
	if ($goto=1){$MM_editRedirectUrl = "nav_list.php?nons=1";}
	else {$MM_editRedirectUrl = "nav_list.php";}
    $MM_fieldsStr =
"name|value|sql|value|titleimg|value|titletext|value|titleti|value|linkfile|value|mfile|value|mcall1|value|mvar2|value|mcall2|value|repeat|value|linkextra|value|mvar1|value|linkfield|value|mvar1val|value|nosqlcode|value|nosql|value|templateid|value|modid|value";
    $MM_columnsStr = "name|',none,''|sql|',none,''|titleimg|',none,''|titletext|',none,''|titleti|none,1,0|linkfile|',none,''|mfile|',none,''|mcall1|',none,''|mvar2|',none,''|mcall2|',none,''|repeat|',none,''|linkextra|',none,''|mvar1|',none,''|linkfield|',none,''|mvar1val|',none,''|nosqlcode|',none,''|nosql|none,1,0|templateid|',none,''|modid|',none,''";
	
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }

$Recordset1__MMColParam = "900000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}

   $Recordset1=$dbcon->Execute("SELECT * FROM navtbl WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?>
<?php
   $templatelab=$dbcon->Execute("SELECT id, name FROM template ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
   $templatelab_numRows=0;
   $templatelab__totalRows=$templatelab->RecordCount();
   	$modlab=$dbcon->Execute("SELECT id, name FROM modules ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $modlab_numRows=0;
   $modlab__totalRows=$modlab->RecordCount();

?>
<?php include ("header.php");?>

<form name="form1" method="POST" action="<?php echo $MM_editAction?>">
             
              
        <table width="100%" border="0" align="center">
          <tr class="banner"> 
            <td colspan="2">Add/Edit Navigation File </td>
          </tr>
          <tr> 
            <td class="name">Navigation Name</td>
            <td><input name="name" type="text" id="name" size="50" value="<?php echo $Recordset1->Fields("name")?>"></td>
          </tr>
          <tr> 
            <td class="name">Title Text</td>
            <td> <p class="text"> 
                <input name="titletext" type="text" id="titletext" size="50" value="<?php echo $Recordset1->Fields("titletext")?>">
                <br>
            </td>
          </tr>
          <tr> 
            <td class="name">Title Image</td>
            <td> <p class="text"> 
                <input name="titleimg" type="text" id="titleimage" size="50" value="<?php echo $Recordset1->Fields("titleimg")?>">
                <br>
              </p></td>
          </tr>
          <tr> 
            <td class="name">&nbsp;</td>
            <td class="name"> <input name="titleti" type="checkbox" id="titleti" value="checkbox" <?php if (($Recordset1->Fields("titleti")) == "1") { echo "CHECKED";} ?>>
              Use Title Image </td>
          </tr>
          <tr> 
            <td class="name"><p>Content</p>
              <p>&nbsp; </p></td>
            <td><textarea name="nosqlcode" cols="60" rows="25" wrap="VIRTUAL" id="sql"><?php echo $Recordset1->Fields("nosqlcode")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name">Template</td>
            <td><select name="templateid" id="templateid">
                <option value="">none</option>
                <?php
  if ($templatelab__totalRows > 0){
    $templatelab__index=0;
    $templatelab->MoveFirst();
    WHILE ($templatelab__index < $templatelab__totalRows){
?>
                <option value="<?php echo  $templatelab->Fields("id")?>"<?php if ($templatelab->Fields("id")==$Recordset1->Fields("templateid")) echo "SELECTED";?>> 
                <?php echo  $templatelab->Fields("name");?> </option>
                <?php
      $templatelab->MoveNext();
      $templatelab__index++;
    }
    $templatelab__index=0;  
    $templatelab->MoveFirst();
  }
?>
              </select> <input name="nosql" type="hidden" id="nosql" value="1"> 
              <?php if ($goto=1) {?>
              <input name="goto" type="hidden" id="goto" value="1"> 
              <?php }?>
            </td>
          </tr><tr>
            <td valign="top" class="name">Module</td>
            <td><select name="modid" id="modid">
                <option value="0">none</option>
                <?php
  if ($modlab__totalRows > 0){
    $modlab__index=0;
    $modlab->MoveFirst();
    WHILE ($modlab__index < $modlab__totalRows){
?>
                <option value="<?php echo  $modlab->Fields("id")?>"<?php if ($modlab->Fields("id")==$Recordset1->Fields("modid")) echo "SELECTED";?>> 
                <?php echo  $modlab->Fields("name");?> </option>
                <?php
      $modlab->MoveNext();
      $modlab__index++;
    }
    $modlab__index=0;  
    $modlab->MoveFirst();
  }
?>
              </select></td>
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
      <?php
  $Recordset1->Close();
  $templatelab->Close();
?>
      <?php include ("footer.php");?>
