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
$Recordset1__MMColParam = "8000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
// *** Insert Record: set Variables

 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {

   // $MM_editConnection = MM_freedomrising_STRING;
  $MM_editTable  = "users";
   $MM_editRedirectUrl = "user_list.php";
   $MM_recordId = "" . $MM_recordId . "";
   $MM_editRedirectUrl = "user_list.php";
   $MM_editColumn = "id";
   $MM_fieldsStr = "name|value|passwordx|value|userlevel|value|email|value";
   $MM_columnsStr = "name|',none,''|password|',none,''|permission|none,none,NULL|email|',none,''|";
 
 
    require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }

?><?php

 $Recordset2=$dbcon->Execute("SELECT id, name FROM per_group") or DIE($dbcon->ErrorMsg());
   $Recordset2_numRows=0;
   $Recordset2__totalRows=$Recordset2->RecordCount();
   $Recordset1=$dbcon->Execute("SELECT * FROM users WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php include ("header.php"); ?>

<h2>Users</h2>
<form method="post" action="<?php echo $MM_editAction?>" name="form1">
  <table border=0 cellpadding=2 cellspacing=0 align="center">
    <tr valign="baseline"> 
      <td nowrap align="right">Name:</td>
      <td> 
        <input type="text" name="name" value="<?php echo $Recordset1->Fields("name")?>" size="40">
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Password:</td>
      <td> 
        <input type="password" name="passwordx" value="<?php echo $Recordset1->Fields("password")?>" size="40">
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Permission:</td>
      <td> 
        <select name="userlevel">
			  <option value="">Set Permission</option>
                  <?php
  if ($Recordset2__totalRows > 0){
    $Recordset2__index=0;
    $Recordset2->MoveFirst();
    WHILE ($Recordset2__index < $Recordset2__totalRows){
?>
                  <OPTION VALUE="<?php echo  $Recordset2->Fields("id")?>"<?php if ($Recordset1->Fields("permission")==$Recordset2->Fields("id")) echo "SELECTED";?>> 
                  <?php echo  $Recordset2->Fields("name");?> </OPTION>
                  <?php
      $Recordset2->MoveNext();
      $Recordset2__index++;
    }
    $Recordset2__index=0;  
    $Recordset2->MoveFirst();
  }
?>
                </select>
      </td>
    </tr>   <tr valign="baseline"> 
            <td nowrap align="right">email:</td>
            <td> <input type="text" name="email" value="<?php echo $Recordset1->Fields("email")?>" size="40"> 
            </td>
          </tr>
    <tr valign="baseline"> 
            <td nowrap align="right"><p><br>
              </p>
              <p>&nbsp; </p></td>
      <td><p>&nbsp;</p>
              <p> 
                <input type="submit" name="Submit" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onClick="return confirmSubmit('Are you sure you want to DELETE this record?')">
              </p>
        </td>
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
   $Recordset2->Close();
?>
