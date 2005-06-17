<?php
  require_once("../Connections/freedomrising.php");  
     if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
	 
	     $MM_editTable  = "contacts_fields";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "admin.php";
	$MM_fieldsStr = "name|value|type|value|camid|value|fieldorder|value";
    $MM_columnsStr = "name|',none,''|type|',none,''|camid|',none,''|fieldorder|',none,''";
  
 require ("../../Connections/insetstuff.php");
  require ("../../Connections/dataactions.php");

		 }
  
  
  if ($_GET[id]) {$passed = $_GET[id];}
  else {$passed = 99999999999999999;}
  
   $Recordset1=$dbcon->Execute("SELECT * from contacts_fields where id  = $passed ") or DIE($dbcon->ErrorMsg());
    $camps=$dbcon->Execute("SELECT * from contacts_campaign ") or DIE($dbcon->ErrorMsg());
     $camps_numRows=0;
   $camps__totalRows=$camps->RecordCount();

   
?>
<?php include ("header.php"); ?>
<h2>&nbsp;</h2>
<form name="form1" method="post" action="">
<table width="95%" border="0" cellspacing="5" cellpadding="0" align="center">
  <tr> 
 <td>Field</td>
      <td> <input name="name" type="text" id="name" value="<?php echo $Recordset1->Fields("name")?>" size="40">       </td>
  </tr>
  <tr>
    <td>Campaign</td>
    <td>
      <select name="camid" >
                <?php
  if ($camps__totalRows > 0){
    $camps__index=0;
    $camps->MoveFirst();
    WHILE ($camps__index < $camps__totalRows){
?>
                <OPTION VALUE="<?php echo  $camps->Fields("id")?>" <?php if ($camps->Fields("id")==$Recordset1->Fields("camid")) echo "SELECTED";?>> 
                <?php echo  $camps->Fields("name");?> </OPTION>
                <?php
      $camps->MoveNext();
      $camps__index++;
    }
    $camps__index=0;  
    $camps->MoveFirst();
  }
?>
              </select>
   </td>
  </tr>
  <tr>
    <td>Fields Type </td>
    <td><input type="radio" name="type" value="1" <?php If (($Recordset1->Fields("type")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="type" value="3" <?php If (($Recordset1->Fields("type")) == "3") { echo "CHECKED";} ?> >
        Multi Line Text 
        <input type="radio" name="type" value="2" <?php If (($Recordset1->Fields("type")) == "2") { echo "CHECKED";} ?> >
        Checkbox </td>
  </tr>
  <tr>
    <td>Order</td>
    <td><input name="fieldorder" type="text" id="fieldorder" value="<?php echo $Recordset1->Fields("fieldorder")?>" size="40"></td>
  </tr>
</table>
<input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"> 
            <?php  if ($userper[98]){ ?>  <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> <?php }?>
          </td>
          </tr>
        </table>
              
	<input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">

 </form>

<?php include ("footer.php");?>


