<?php
  require("Connections/freedomrising.php");
  ?>

<?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();
?>
<?php
  // *** Update Record: set variables
  
 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
              //Delete cached versions of output file
 
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "helpnotes";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
	//if ($main == 1) {
	$response=1;
	//$MM_editRedirectUrl = "http://www.radicaldesigns.org/index2.php?notes=$notes&helpid=$helpid&user=$user&site=$site";//"http://www.radicaldesigns.org/help/help.php";
	//?notes=$notes&helpid=$helpid&user=$user&site=$site";
	//}
//else {
  //  $MM_editRedirectUrl = "help_notes.php?response=1"; }
    $MM_fieldsStr = "notes|value|helpid|value|user|value";
    $MM_columnsStr = "notes|',none,''|helpid|',none,''|user|',none,''";
  
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
  
?>
<?php
   $type=$dbcon->Execute("SELECT * FROM help WHERE id = $id") or DIE($dbcon->ErrorMsg());
   $type_numRows=0;
   $type__totalRows=$type->RecordCount();
   if (isset($update)) {
   $type2=$dbcon->Execute("SELECT notes FROM helpnotes WHERE id = $update") or DIE($dbcon->ErrorMsg());
   }
?>
<html><title>Help - Activist CMS</title>
<link href="managment.css" rel="stylesheet" type="text/css">
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<h2 align="center"> Help Notes</h2>
<?php if ($response != 1) { ?>
<form name="form" ACTION="<?php echo $MM_editAction?>"  METHOD="POST" >

        
  <p><a href="module_nav_edit.php?id=<?php echo $HTTP_GET_VARS["id"]; ?>"> 
    </a></p>   
        
  <table width="90%" border="0" align="center">
    <tr> 
      <td valign="top" class="name"> <div align="left">File Title</div></td>
      <td class="pagetitle"><?php echo  $type->Fields("title") ?> </td>
    </tr>
    <tr> 
      <td valign="top" class="name">Section</td>
      <td class="pagetitle"><?php echo $type->Fields("section") ?> </td>
    </tr>
    <tr> 
      <td valign="top" class="name">Notes</td>
      <td><textarea name=notes cols=65 rows=20 wrap=VIRTUAL id="notes"><?php 
	  if (isset($update)){
	 echo $type2->Fields("notes");
	  }
	  ?></textarea></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="name"><input type="hidden" name="helpid" value="<?php echo $id ?>"><input type="hidden" name="user" value="<?php echo $ID ?>"></td>
    </tr>
  </table>
 
          
        <p> 
          <input name="submit" type="submit" value="Save Changes">
          <?php if (isset($HTTP_GET_VARS["add"])== TRUE) { ?>
          <input type="hidden" name="MM_insert" value="true">
                <?php 
		}
		else { ?>
		<input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
                <input type="hidden" name="MM_update" value="true">
                <?php } ?>
                <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["update"]; ?>">
      </form>
<?php }
else {
?>
<p><strong>Your comments have been posted</strong></p>
<p><?php echo nl2br($notes) ?>&nbsp;</p>
<?php if ($add == 1){ ?>
<form action="http://www.radicaldesigns.org/help/help_notes.php" method="post" name="form1" class="name">
  <strong>Do wish to add this comment to the central Activist CMS Help Database 
  </strong> 
  <input name="Submit" type="submit" class="name" value="yes">
  <input type="hidden" name="notes" value="<?php echo $notes ?>">
   <?php  $u2=$dbcon->Execute("SELECT name FROM users WHERE id = $ID") or DIE($dbcon->ErrorMsg());?>
  <input type="hidden" name="user" value="<?php echo $u2->Fields("name") ?>">
  <input type="hidden" name="helpid" value="<?php echo $id ?>">
  <input type="hidden" name="id" value="<?php echo $id ?>">
   <input type="hidden" name="add" value="1">
  <input type="hidden" name="site" value="<?php echo  $Web_url?>">
  <input type="hidden" name="MM_insert" value="true">
  
  
</form><?php } ?>

<p> <a href="javascript:window.close()"><B>CLOSE WINDOW</B></a>
  <?php }?>
</p>
</body></html>

