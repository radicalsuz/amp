<?php
$modid= 19;

require("Connections/freedomrising.php");

  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();

?><?php
// *** Insert Record: set Variables
 
if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {

   // $MM_editConnection = MM__STRING;
   $MM_editColumn = "id";
   $MM_editTable  = "class";
   $MM_editRedirectUrl = "edittypes.php";
   $MM_recordId = "" . $MM_recordId . "";
   $MM_fieldsStr =
"type|value|select|value|image|value|checkbox|value|cap|value|up|value|description|value|uselink|value|linkurl|value|order|value|useclass|value|usenav|value|image2|value|css|value|flash|value";
    $MM_columnsStr = "class|',none,''|url|',none,''|image|',none,''|useimage|none,1,0|imgcap|',none,''|up|',none,''|description|',none,''|uselink|none,1,0|linkurl|',none,''|textorder|',none,''|useclass|none,1,0|usenav|none,1,0|image2|',none,''|css|',none,''|flash|',none,''";
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
  
?>

<?php

$subtype__MMColParam = "90000000";
if (isset($HTTP_GET_VARS["id"]))
  {$subtype__MMColParam = $HTTP_GET_VARS["id"];}
   $subtype=$dbcon->Execute("SELECT * FROM class WHERE id = " . ($subtype__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $subtype_numRows=0;
   $subtype__totalRows=$subtype->RecordCount();
?><?php
   $Recordset1=$dbcon->Execute("SELECT id, title FROM articles order by title asc") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
 $templatelab=$dbcon->Execute("SELECT id, name FROM template ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
   $templatelab_numRows=0;
   $templatelab__totalRows=$templatelab->RecordCount();

?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $subtype_numRows = $subtype_numRows + $Repeat1__numRows;
?><?php include ("header.php");?>       
<h2><?php echo helpme(""); ?><?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>Add<?php } else { ?>Update <?php }?>&nbsp; Article Class</h2>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>">
             
              <table width="100%" border="0">
			  <tr> 
                  <td colspan="2" class="intitle"><?php echo helpme("Class Information"); ?>Class Information</td>
                </tr>
                <tr> 
                  <td class="name">ID</td>
                  <td> <?php echo $subtype->Fields("id")?> </td>
                </tr>
                <tr> 
                  <td class="name">Class Name</td>
                  <td> <input name="type" type="text" id="type" value="<?php echo $subtype->Fields("class")?>" size="50"></td>
                </tr>
                <tr> 
                <tr> 
                  <td class="name">Description</td>
                  <td> <textarea name="description" cols="45" wrap="VIRTUAL" id="description"><?php echo $subtype->Fields("description")?></textarea> 
                  </td>
                </tr>
                <tr> 
                  <td colspan="2" class="intitle"><?php echo helpme("Link Information"); ?>Link Information</td>
                </tr>
                <tr> 
                  <td class="name">Page to link to</td>
                  <td> <select name="select">
                      <?php
  if ($Recordset1__totalRows > 0){
    $Recordset1__index=0;
    $Recordset1->MoveFirst();
    WHILE ($Recordset1__index < $Recordset1__totalRows){
?>
                      <OPTION VALUE="<?php echo  $Recordset1->Fields("id")?>"<?php if ($Recordset1->Fields("id")==$subtype->Fields("url")) echo "SELECTED";?>> 
                      <?php echo  $Recordset1->Fields("title");?> </OPTION>
                      <?php
      $Recordset1->MoveNext();
      $Recordset1__index++;
    }
    $Recordset1__index=0;  
    $Recordset1->MoveFirst();
  }
?>
                    </select> </td>
                </tr>
                <tr> 
                  <td class="name">Link to other URL</td>
                  <td> <input name="uselink" type="checkbox" id="uselink" value="checkbox" <?php If (($subtype->Fields("uselink")) == "1") { echo "CHECKED";} ?>> 
                  </td>
                </tr>
                <tr> 
                  <td class="name">Other URL to link to</td>
                  <td> <input type="text" name="linkurl" size="45" value="<?php echo $subtype->Fields("linkurl")?>"> 
                  </td>
                </tr>
                <tr> 
                  <td class="name">Order</td>
                  <td><input name="order" type="text" id="order" value="<?php echo $subtype->Fields("textorder")?>" size="10"></td>
                </tr>
                <tr> 
                  <td class="name">Use Class</td>
                  <td><input name="useclass" type="checkbox" id="useclass" value="checkbox" <?php If (($subtype->Fields("useclass")) == "1") { echo "CHECKED";} ?>></td>
                </tr>
                <tr> 
                  <td class="name">List on main navigation bar</td>
                  <td><input name="usenav" type="checkbox" id="usenav" value="checkbox" <?php If (($subtype->Fields("usenav")) == "1") { echo "CHECKED";} ?>></td>
                </tr>
                <tr> 
                  <td colspan="2" class="intitle"><?php echo helpme("Images"); ?>Images</td>
                </tr>
                <tr> 
                  <td class="name">Image</td>
                  <td> <input type="text" name="image" size="50" value="<?php echo $subtype->Fields("image")?>"> 
                  </td>
                </tr>
                <tr> 
                  <td class="name">Use image instead of type name </td>
                  <td> <input <?php If (($subtype->Fields("useimage")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox" value="checkbox"> 
                  </td>
                </tr>
                <tr> 
                  <td class="name">Image 2</td>
                  <td> <input type="text" name="image2" size="50" value="<?php echo $subtype->Fields("image2")?>"> 
                  </td>
                </tr>
                <tr> 
                  <td class="name">Image 2 Caption</td>
                  <td> <textarea name="cap" wrap="VIRTUAL" cols="45" rows="3"><?php echo $subtype->Fields("imgcap")?></textarea></td>
                </tr>
                <tr> 
                  <td colspan="2" class="intitle"><?php echo helpme("Style Features"); ?>Style Features</td>
                </tr>
                <tr> 
                  <td class="name">Different css file to use</td>
                  <td> <input name="css" type="text" value="<?php echo $subtype->Fields("css")?>" size="45" > 
                  </td>
                </tr>
                <tr> 
                  <td class="name">Flash Navigation File to Use</td>
                  <td><input name="flash" type="text" id="flash" value="<?php echo $subtype->Fields("flash")?>" size="45" > 
                  </td>
                </tr>
                <tr> 
                  <td class="name">Special Field</td>
                  <td><input name="up" type="text" id="up" value="<?php echo $subtype->Fields("up")?>" size="45" > 
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
                      <option value="<?php echo  $templatelab->Fields("id")?>"<?php if ($templatelab->Fields("id")==$subtype->Fields("templateid")) echo "SELECTED";?>> 
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
              </table>
              
         <input type="submit" name="Submit" value="Submit">
		
        <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?><input type="hidden" name="MM_insert" value="true">
		<?php 
		}
		else { ?>
		<input type="hidden" name="MM_recordId" value="<?php echo $subtype->Fields("id")?>"><input type="hidden" name="MM_update" value="true"><?php } ?>
</form>
            <form name="delete" method="POST" action="<?php echo $MM_editAction?>">
              <input type="submit" name="delete" value="delete">
  		<input type="hidden" name="MM_delete" value="true">
  		<input type="hidden" name="MM_recordId" value="<?php echo $subtype->Fields("id")?>">
</form>
             <p><a href="nav_position.php?class=<?php echo $HTTP_GET_VARS["id"]; ?>"> 
                Edit Navigation Files for Lists</a>
				<br>
                <!--
				<a href="class_nav_edit.php?id=<?php echo $HTTP_GET_VARS["id"]; ?>"> 
                Edit Navigation Files for Content</a>--></p>
              <?php
  $subtype->Close();
?>
              <?php
  $Recordset1->Close();
  $templatelab->Close();
?>
            </p>
            <?php include ("footer.php");?>
