<?php
  require("Connections/freedomrising.php");
  include("FCKeditor/fckeditor.php");
?><?php
 if (isset($HTTP_GET_VARS["id"])) { $id= $HTTP_GET_VARS["id"];}
 else {$id= 90000000;}
   $modsel=$dbcon->Execute("SELECT * FROM modules WHERE  id = $id") or DIE($dbcon->ErrorMsg()); 


  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();


if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
    $MM_editTable  = "modules";
    $MM_editColumn = "id";
    $MM_recordId = $HTTP_POST_VARS["MM_recordId"];
    $MM_editRedirectUrl = "module_list.php";
    $MM_fieldsStr =
"id|value|name|value|userdatamod|value|userdatamodid|value|file|value|perid|value|article|value|publish|value";
    $MM_columnsStr = "id|',none,''|name|',none,''|userdatamod|',none,''|userdatamodid|',none,''|file|',none,''|perid|',none,''|navhtml|',none,''|publish|',none,''";
	 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");

  }
?><?php include ("header.php"); ?>

<h2>Module Configuration</h2>
<form name="form1" method="post" action="<?php echo $MM_editAction?>">
        <table width="100%" border="0" cellspacing="0" cellpadding="5" class="table">
          <tr> 
            <td class="name">Name</td>
            <td><input name="name" type="text" id="name" size="45" value="<?php echo $modsel->Fields("name");?>"></td>
          </tr>
          <tr> 
            <td class="name">Permission</td>
            <td><input name="perid" type="text" id="file3" size="45" value="<?php echo $modsel->Fields("perid");?>"></td>
          </tr>
          <tr> 
            <td class="name">Publish</td>
            <td><input name="publish" type="checkbox" id="publish" value="1" <?php if ($modsel->Fields("publish") == 1) {echo "checked";}?>></td>
          </tr>
          <tr> 
            <td colspan="2" class="name"> Navigation HTML:<br>
 <textarea name="article" cols="65" rows="20" wrap="VIRTUAL"><?php
		$text2 = $modsel->Fields("navhtml");
		
		 echo $text2; ?></textarea> </td>
          </tr>
          <tr> 
            <td class="name">Defual File</td>
            <td><input name="file" type="text" id="file" size="45" value="<?php echo $modsel->Fields("file");?>"></td>
          </tr>
          <tr> 
            <td class="name">User Data Module</td>
            <td><input name="userdatamod" type="checkbox" id="userdatamod" value="1" <?php if ($modsel->Fields("userdatamod") == 1) {echo "checked";}?>></td>
          </tr>
          <tr> 
            <td class="name">User Data Module</td>
            <td><input name="userdatamodid" type="text" id="userdatamodid" size="45" value="<?php echo $modsel->Fields("userdatamodid");?>"></td>
          </tr>
		       <tr> 
            <td class="name">Module ID</td>
            <td><input name="id" type="text"  size="45" value="<?php echo $modsel->Fields("id");?>"></td>
          </tr>
          <tr> 
            <td class="name">&nbsp;</td>
            <td>&nbsp;</td>
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