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
  
 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
              //Delete cached versions of output file
 
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "help";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "help_list.php";
    $MM_fieldsStr = "file1|value|html|value|title|value|notes|value|section|value|sorder|value|type|value";
    $MM_columnsStr = "file1|',none,''|html|',none,''|title|',none,''|notes|',none,''|section|',none,''|sorder|',none,''|type|',none,''";
  
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
  
$type__MMColParam = "999999999999";
if (isset($HTTP_GET_VARS["id"]))
  {$type__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
   $type=$dbcon->Execute("SELECT * FROM help WHERE id = " . ($type__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $type_numRows=0;
   $type__totalRows=$type->RecordCount();
?>



<?php include ("header.php"); ?>
<h2 align="right"> Add / Edit Help File</h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST">
        <p><a href="module_nav_edit.php?id=<?php echo $HTTP_GET_VARS["id"]; ?>"> 
          </a></p>   
        <table width="90%" border="0" align="center">
          <tr> 
            <td valign="top"> <div align="left"><b>Help ID#</b> </div></td>
            <td> <?php echo $type->Fields("id")?> </td>
          </tr>
          <tr> 
            <td valign="top" class="name"> <div align="left">File Name</div></td>
            <td><input name="file1" value="<?php echo  $type->Fields("file1") ?>" size="40" > 
            </td>
          </tr>
          <tr> 
            <td valign="top" class="name"> <div align="left">File Title</div></td>
            <td><input name="title" value="<?php echo  $type->Fields("title") ?>" size="40" > 
            </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Section</td>
            <td><input name="section" value="<?php echo $type->Fields("section") ?>" size="55" > 
            </td>
          </tr>
          <tr> 
            <td valign="top" class="name"> <div align="left">Section Order</div></td>
            <td> <input name="sorder" size="10" value="<?php echo $type->Fields("sorder") ?>"            > 
            </td>
          </tr>
          <tr> 
            <td valign="top" class="name"> <p align="left">Article Text</p>
              <p align="left">&nbsp;</p></td>
            <td> <textarea name="html" rows=20 wrap=VIRTUAL cols=65><?php echo $type->Fields("html")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Notes</td>
            <td><textarea name=notes cols=65 rows=10 wrap=VIRTUAL id="notes"><?php echo $type->Fields("notes")?></textarea></td>
          </tr>
          <tr>
            <td valign="top" class="name">Type</td>
            <td><select name="type" id="type">
                <?php if ($type->Fields("type") != NULL) {?>
				<option value="<?php if ($type->Fields("type") == "File")
				{echo "";}
				else { echo $type->Fields("type"); } ?>" selected><?php echo $type->Fields("type") ?></option><?php }?>
				<option value="">File</option>
                <option value="Tutorial">Tutorial</option>
                <option value="Overview">Overview</option>
              </select></td>
          </tr>
        </table>
 
          
        <p> 
          <input name="submit" type="submit" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
                <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
                <input type="hidden" name="MM_insert" value="true">
                <?php 
		}
		else { ?>
                <input type="hidden" name="MM_update" value="true">
                <?php } ?>
                <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
      </form>


<?php include("footer.php"); ?>

