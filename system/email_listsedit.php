<?php
$modid=9;
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
$Recordset1__MMColParam = "8000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
// *** Insert Record: set Variables

 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {

   // $MM_editConnection = MM_freedomrising_STRING;
   $MM_editTable  = "lists";
   $MM_editRedirectUrl = "email_lists.php";
   $MM_recordId = "" . $MM_recordId . "";
      $MM_editColumn = "id";
   $MM_fieldsStr = "name|value|description|value|publish|value";
   $MM_columnsStr = "name|',none,''|description|',none,''|publish|',none,''";
 
    require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }

?><?php


   $Recordset1=$dbcon->Execute("SELECT * FROM lists WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php include ("header.php"); ?>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="banner">
          <td>Mailing Lists</td>
        </tr>
      </table>
      <form method="post" action="<?php echo $MM_editAction?>" name="form1">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID 
        # <?php echo $Recordset1->Fields("id")?><br>
        <table border=0 cellpadding=2 cellspacing=0 align="center">
          <tr valign="baseline"> 
            <td nowrap align="right">List Name:</td>
            <td> <input type="text" name="name" value="<?php echo $Recordset1->Fields("name")?>" size="40"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right">Description:</td>
            <td> <textarea name="description" cols="40" rows="4" wrap="VIRTUAL"><?php echo $Recordset1->Fields("description")?></textarea> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><p>Publish<br>
              </p>
              <p>&nbsp; </p></td>
            <td><p>
               <input <?php If (($Recordset1->Fields("publish")) == "1") { echo "CHECKED";} ?> type="checkbox" name="publish" value="1">
              </p>
              <p> 
                <input type="submit" name="Submit" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onClick="return confirmSubmit('Are you sure you want to DELETE this record?')">
              </p></td>
          </tr>
        </table>
        <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
        <input type="hidden" name="MM_insert" value="true">
        <?php 
		}
		else { ?>
        <input type="hidden" name="MM_update" value="true">
        <?php } ?>
        <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
      </form>  
      <p>&nbsp;</p>


<?php include ("footer.php"); ?><?php
  $Recordset1->Close();
   
?>
