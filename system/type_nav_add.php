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
  
 if (isset($MM_insert)) {
   //Delete cached versions of output file
   
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "nav";
     $MM_editRedirectUrl = "type_nav_edit.php?";
    $MM_fieldsStr = "navid|value|position|value|moduleid|value";
    $MM_columnsStr = "navid|none,none,NULL|position|',none,''|typeid|none,none,NULL";
  
  require ("../Connections/insetstuff.php");
  }

require ("../Connections/dataactions.php");
ob_end_flush();
 
$mod_id = $HTTP_GET_VARS["id"];

    $allnav=$dbcon->Execute("SELECT modules.name as mod, navtbl.name, navtbl.id FROM navtbl, modules where modules.id= navtbl.modid order by modules.name asc, navtbl.name asc") or DIE($dbcon->ErrorMsg());
   $allnav_numRows=0;
   $allnav__totalRows=$allnav->RecordCount();

 include ("header.php"); ?>
<h2>Navagition Order Selection</h2>

<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
   <tr class="intitle"> 
    <td></td>
              <td>Naviagtion File</td>
              <td>Position</td>
			 
  </tr>

  <tr>
    <td><form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="Form1">
	
	<input type="submit" name="Submit" value="Insert">&nbsp;</td>
    <td>
	<select name="navid">
                  <?php
  if ($allnav__totalRows > 0){
    $allnav__index=0;
    $allnav->MoveFirst();
    WHILE ($allnav__index < $allnav__totalRows){
?>
                  <OPTION VALUE="<?php echo  $allnav->Fields("id")?>"> 
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
					</option>
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
    <input type="hidden" name="moduleid" value="<?php echo $HTTP_GET_VARS["id"] ?>">
	
  
 <input type="hidden" name="MM_insert" value="true">

</form>
				</td>
	
  </tr>
  
</table>
		
<?php include("footer.php"); ?>