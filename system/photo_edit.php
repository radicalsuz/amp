<?php
$modid=8;

  require("Connections/freedomrising.php");
  include("Connections/menu.class.php");
  $obj = new Menu;

  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  
?><?php

  
   if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) ) {
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "gallery";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "photo_list.php";
    $MM_fieldsStr = "sectoin|value|img|value|caption|value|photoby|value|date|value|byemail|value|checkbox|value|select2|value|relsection1|value|relsection2|value|season|value";
    $MM_columnsStr = "section|',none,1|img|',none,''|caption|',none,''|photoby|',none,''|date|',none,''|byemail|',none,''|publish|none,1,0|galleryid|none,none,NULL|relsection1|none,none,NULL|relsection2|none,none,NULL|season|',none,''";
  
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");

   }

$Recordset1__MMColParam = "900000000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}

   $Recordset1=$dbcon->Execute("SELECT * FROM gallery WHERE id = " . ($Recordset1__MMColParam) . " ") or DIE($dbcon->ErrorMsg());
   
   $Recordset1__totalRows=$Recordset1->RecordCount();
  if (isset($HTTP_GET_VARS["id"])) {$typevar=$Recordset1->Fields("section");}
else {$typevar=1;}
    $typelab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$typevar."") or DIE($dbcon->ErrorMsg());
	$timber2=$dbcon->Execute("SELECT id, galleryname FROM gallerytype") or DIE($dbcon->ErrorMsg());
   $timber2_numRows=0;
   $timber2__totalRows=$timber2->RecordCount();
   
   if ($_GET[id]) {$secvar1=$Recordset1->Fields("relsection1");}
else {$secvar1=1;}
   $rel1q=$dbcon->Execute("SELECT id, type FROM articletype where id =$secvar1");

   if ($_GET[id]) {$secvar2=$Recordset1->Fields("relsection2");}
else {$secvar2=1;}
   $rel2q=$dbcon->Execute("SELECT id, type FROM articletype where id =$secvar2") ;
   
?>

<?php include ("header.php"); ?>

<h2>Edit/Add Photo </h2>
<div align="center"><?php if ($_GET[id]){
		echo "<img src =\"../img/pic/".$Recordset1->Fields("img")."\" align=center>";
		}?>
		<?php if ($_GET[p]){
		echo "<img src =\"../img/pic/".$_GET[p]."\" align=center>";
		}?>
		</div>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="Form1">
        <table width="100%" border="0" class="name">
          <tr> 
            <td width="78" align="right"><div align="left">Gallery</div></td>
            <td width="211"> <select name="select2">
                <option value="0">none</option>
                <?php
  if ($timber2__totalRows > 0){
    $timber2__index=0;
    $timber2->MoveFirst();
    WHILE ($timber2__index < $timber2__totalRows){
?>
                <option value="<?php echo  $timber2->Fields("id")?>"<?php if ($timber2->Fields("id")==$Recordset1->Fields("galleryid")) echo "SELECTED";?>> 
                <?php echo  $timber2->Fields("galleryname");?> </option>
                <?php
      $timber2->MoveNext();
      $timber2__index++;
    }
    $timber2__index=0;  
    $timber2->MoveFirst();
  }
?>
              </select></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Section</div></td>
            <td> <select name="section">
                <OPTION VALUE="<?php echo  $typelab->Fields("id")?>" SELECTED><?php echo  $typelab->Fields("type")?></option>
				 <?php echo $obj->select_type_tree(0); ?>
              </select> </td>
          </tr>
		  <?php if (isset($relsection1id)) {?>
		  <tr> 
            <td valign="top"> <span align="left" class="name"><?php echo $relsection1label ;?></span></td>
            <td class="text"> <select name="relsection1">
                <OPTION VALUE="<?php echo  $Recordset1->Fields("relsection1")?>" SELECTED><?php echo  $rel1q->Fields("type")?></option>
                <?php echo $obj->select_type_tree($relsection1id); ?> </Select> 
            </td>
          </tr>
          <?php } 
	 if (isset($relsection2id)) {
	
	?>
          <tr> 
            <td valign="top"> <span align="left" class="name"><?php echo $relsection2label ;?></span></td>
            <td class="text"> <select name="relsection2">
                <option value="<?php echo  $Recordset1->Fields("relsection2")?>" selected><?php echo  $rel2q->Fields("type")?></option>
                <?php echo $obj->select_type_tree($relsection2id); ?> </select></td>
          </tr>
		  <?php }   
		   if (isset($MM_season)) {	?>
		          <tr> 
            <td valign="top"> <span align="left" class="name">Season</span></td>
            <td class="text"> <select name="season">
                <option value="<?php echo  $Recordset1->Fields("season")?>" selected><?php echo $Recordset1->Fields("season")?></option>
              <option value="">none</option>
			    <option value="Winter">Winter</option>
				<option value="Spring">Spring</option>
				<option value="Summer">Summer</option>
				<option value="Fall">Fall</option>
				 </select></td>
          </tr>   
		   
		  <?php }   ?>
          <tr> 
            <td align="right" width="78"><div align="left">Image</div></td>
            <td width="211"> <input type="text" name="img" size="45" value="<?php if ($_GET[id]){echo $Recordset1->Fields("img");} else {echo $_GET[p];}?>"> 
              <br> &nbsp;<a href="imgdir.php" target="_blank">view images</a>&nbsp;&nbsp;<a href="imgup.php" target="_blank">upload 
              Image</a> </td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">Caption</div></td>
            <td width="211"> <textarea name="caption" cols="40" wrap="VIRTUAL" rows="3"><?php echo $Recordset1->Fields("caption")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">Photo By</div></td>
            <td width="211"> <input type="text" name="photoby" size="45" value="<?php echo $Recordset1->Fields("photoby")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">Date</div></td>
            <td> <input type="text" name="date" value="<?php echo $Recordset1->Fields("date")?>">
              2001-10-22</td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">By Email</div></td>
            <td width="211"> <input type="text" name="byemail" size="45" value="<?php echo $Recordset1->Fields("byemail")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" width="78"><div align="left">Publish</div></td>
            <td width="211"> <input <?php If (($Recordset1->Fields("publish")) == "1") { echo "CHECKED";}  if (!$_GET[id]) {echo "checked";}?> type="checkbox" name="checkbox" value="1"> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="right"><div align="left"> 
                <input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
                <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
              </div></td>
          </tr>
        </table>
        <p>&nbsp; </p>
		
      </form>
<?php include("footer.php"); ?>

