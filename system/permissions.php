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
?><?php

 $permissions=$dbcon->Execute("SELECT * FROM per_description where publish = 1 ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
$permissions_numRows=0;
$permissions__totalRows=$permissions->RecordCount();
$Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $permissions_numRows = $permissions_numRows + $Repeat1__numRows;
// *** Insert Record: set Variables
if (isset($MM_insert)){
  
   $MM_editTable  = "per_group";
   $MM_fieldsStr = "subsite|value|name|value|description|value";
   $MM_columnsStr = "subsite|',none,''|name|',none,''|description|none,none,NULL";

  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");

} //end insert from form

if  (isset($MM_update)){
$groupid= $HTTP_GET_VARS["id"];}


if  (isset($MM_insert)){ 
$newrec=$dbcon->Execute("SELECT id FROM per_group ORDER BY id desc LIMIT 1") or DIE($dbcon->ErrorMsg());  
$groupid = $newrec->Fields("id"); 

while (($Repeat1__numRows-- != 0) && (!$permissions->EOF)) 
   { 
if ($HTTP_POST_VARS[$permissions->Fields("id")] != $null) //insert into permision table
  {$perid=($permissions->Fields("id"));
 $MM_editTable  = "permission";
  $MM_fieldsStr = "perid|value|groupid|value";
   $MM_columnsStr = "perid|none,none,NULL|groupid|none,none,NULL"; 
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
	}
	$Repeat1__index++;
  $permissions->MoveNext();
  } 
  }//end insert
// $MM_editRedirectUrl = "email.php?thank=1";
//	 header ("Location: $MM_editRedirectUrl");
//}

if  (isset($MM_update)){  //start update
$groupid= $HTTP_GET_VARS["id"];
$MM_update = ($null);
while (($Repeat1__numRows-- != 0) && (!$permissions->EOF)) 
   { //start repeat
  $instance = ($HTTP_POST_VARS[$permissions->Fields("id")]);
 
  //echo  $HTTP_POST_VARS[$permissions->Fields("id")];
//if ($instance != FALSE) //start insert/update
 // {
  if ($instance == 500){ //insert
  $perid = $permissions->Fields("id");
  $MM_insert=1; 
//  else { //update
//   $MM_update=1;
//   $MM_recordId = $HTTP_POST_VARS[$permissions->Fields("id")];
//  $MM_editColumn = "id";
//    $MM_recordId = "" . $MM_recordId . "";
//	}
	$MM_editTable  = "permission";
  $MM_fieldsStr = "perid|value|groupid|value";
   $MM_columnsStr = "perid|none,none,NULL|groupid|none,none,NULL"; 
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");}
	//}//end start/insert
//$supvar = ('\$b'.$perid;
//echo $supvar;

	if ($instance == ($null)) { //start delete
	
	$perid = $permissions->Fields("id");
$supvar= "b".$perid;
	 $MM_recordId = ($HTTP_POST_VARS["$supvar"]);
	if  ($MM_recordId != $null){
	$MM_delete = 1;
  $MM_editColumn = "id";  
$MM_editTable  = "permission";
  
	require ("../Connections/insetstuff.php");
   require ("../Connections/dataactions.php");}
}//end deletet	
	 
	
	$Repeat1__index++;
  $permissions->MoveNext();
  } //end repeat 
  
   header ("Location: permissions_list.php");
  }
 
  //end update
	
   
?>
<?php 
$Recordset1__MMColParam = "8000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
$Recordset1=$dbcon->Execute("SELECT * FROM per_group WHERE id = $Recordset1__MMColParam") or DIE($dbcon->ErrorMsg());
?>
<?php include("header.php");?>
				  

      <form method="POST" action="<?php echo $MM_editAction?>" name="form1">
              <table width="90%" border=0 align="center" cellpadding=2 cellspacing=0>
                <tr valign="baseline"> 
                  <td nowrap align="right" class="form"> Name:</td>
				 
                  <td> <input type="text" name="name" value="<?php echo $Recordset1->Fields("name")?>" size="45"> 
                  </td>
                </tr>
                <tr valign="baseline"> 
                  <td nowrap align="right" class="form">Description:</td>
                  <td><textarea name="discription" cols="35" rows="4" wrap="VIRTUAL"><?php echo $Recordset1->Fields("description")?></textarea></td>
                </tr>
				<tr valign="baseline"> 
                  <td nowrap align="right" class="form"> Subsite:</td>
				 
                  <td> <input type="text" name="subsite" value="<?php echo $Recordset1->Fields("subsite")?>" size="45"> 
                  </td>
                </tr>
                <tr valign="baseline"> 
                  <td colspan="2" align="right" nowrap class="form"><div align="center"><strong><br>
                      Permissions</strong></div></td>
                </tr>
                <?php while (($Repeat1__numRows-- != 0) && (!$permissions->EOF)) 
   { 

$instance=$dbcon->Execute("SELECT id FROM permission WHERE groupid = ".$Recordset1__MMColParam." and perid= ".$permissions->Fields("id")." LIMIT 1") or DIE($dbcon->ErrorMsg());
		$inst=$instance->Fields("id");
			$instance->Close();?>
                <tr valign="baseline"> 
                  <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                           <td width="9%" ><input name="<?php echo ($permissions->Fields("id")); ?>" 
						type="checkbox" id="<?php echo $permissions->Fields("id"); ?>" value="<?php echo ("$inst"); ?><?php if (empty($inst)){
						echo "500"; }?>" <?php 
			
			if (isset($inst)){ echo "checked";} ?>><input name="b<?php echo ($permissions->Fields("id")); ?>" type="hidden" value="<?php echo ("$inst"); ?>">
                         <?php echo $permissions->Fields("description"); ?></td>
                  <td width="91%" colspan="2"> <div align="left"></div>
                    <?php echo $permissions->Fields("name"); ?></td>
                     
                      </tr>
                    </table></td>
                </tr>
                <?php $Repeat1__index++;
  $permissions->MoveNext();
}?>
                <tr valign="baseline"> 
                  <td nowrap align="right" class="form">&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr valign="baseline"> 
                  <td nowrap align="right" class="form">&nbsp;</td>
                  <td><input type="submit" name="Submit" value="Submit">
                <?php if (($HTTP_GET_VARS["id"])== ($null)) { ?>
                <input type="hidden" name="MM_insert" value="true">
		<?php 
		}
		else { ?>
		<input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>"><input type="hidden" name="MM_update" value="true"><?php } ?> </td>
                </tr>
              </table>
  
</form>
<form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="hidden" name="MM_delete" value="true">
	 <input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>">
	<input type="submit" name="Submit2" value="Delete"></form>

<?php $Recordset1->Close();?>
<? include("footer.php"); ?>