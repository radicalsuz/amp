<?php
$modid=1;
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
// *** Insert Record: set Variables

 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {

   // $MM_editConnection = MM__STRING;
   $MM_editTable  = "eventtype";
   $MM_editRedirectUrl = "calendar_type_list.php";
   $MM_editColumn = "id";
	$MM_recordId = "" . $MM_recordId . "";
   $MM_fieldsStr = "name|value";
   $MM_columnsStr = "name|',none,''";
}
    require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   
$Recordset1__MMColParam = '90000000000';
if (isset($HTTP_GET_VARS["id"]))
 {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
   $addarea=$dbcon->Execute("SELECT * FROM eventtype ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $addarea_numRows=0;
   $addarea__totalRows=$addarea->RecordCount();
   $called=$dbcon->Execute("SELECT * FROM eventtype where id = ".$Recordset1__MMColParam."") or DIE($dbcon->ErrorMsg());
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $addarea_numRows = $addarea_numRows + $Repeat1__numRows;
?><?php include("header.php"); ?>
<h2> 
        <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
        Add 
        <?php } else { ?>
        Update 
        <?php }?>
        &nbsp;Calendar Type</h2>
            <p><b>Current Types:</b> 
              <?php while (($Repeat1__numRows-- != 0) && (!$addarea->EOF)) 
   { 
?>
              <?php echo $addarea->Fields("name")?> | 
              <?php
  $Repeat1__index++;
  $addarea->MoveNext();
}
?>
            </p>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>">
              <table width="90%" border="0" align="center">
                <tr> 
                  <td class="name">Event Type Name</td>
                  <td> <input type="text" name="name" size="50" value="<?php echo $called->Fields("name") ?>"> </td>
                </tr>
              </table>
  <p> 
   <input type="submit" name="Submit" value="Submit">
		
        <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?><input type="hidden" name="MM_insert" value="true">
		<?php 
		}
		else { ?>
		<input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>"><input type="hidden" name="MM_update" value="true"><?php } ?>
      </form>
<form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  		<input type="submit" name="delete" value="Delete">
  		<input type="hidden" name="MM_delete" value="true">
  		<input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
</form>
<?php
  $addarea->Close();
   $called->Close();
?><?php include("footer.php"); ?>
