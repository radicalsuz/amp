<?php
  require("Connections/freedomrising.php");
 if (isset($id)){ $modidselect=$dbcon->Execute("SELECT id from modules where userdatamodid=$id") or DIE($dbcon->ErrorMsg());
 
$modid=$modidselect->Fields("id");}
   	$list=$dbcon->Execute("SELECT id, name from lists ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $list_numRows=0;
   $list__totalRows=$list->RecordCount();
   
  
   
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  

 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {

    $MM_editTable  = "modfields";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
$MM_fieldsStr ="field11text|value|field12text|value|field13text|value|field14text|value|field15text|value|field16text|value|field17text|value|field18text|value|field19text|value|field20text|value|11ftype|value|12ftype|value|13ftype|value|14ftype|value|15ftype|value|16ftype|value|17ftype|value|18ftype|value|19ftype|value|20ftype|value|11pub|value|12pub|value|13pub|value|14pub|value|15pub|value|16pub|value|17pub|value|18pub|value|19pub|value|20pub|value|field1text|value|field2text|value|field3text|value|field4text|value|field5text|value|field6text|value|field7text|value|field8text|value|field9text|value|field10text|value|1ftype|value|2ftype|value|3ftype|value|4ftype|value|5ftype|value|6ftype|value|7ftype|value|8ftype|value|9ftype|value|10ftype|value|name|value|1pub|value|2pub|value|3pub|value|4pub|value|5pub|value|6pub|value|7pub|value|8pub|value|9pub|value|10pub|value|modidinput|value|modidresponse|value|sourceid|value|enteredbyid|value|useemail|value|mailto|value|subject|value|redirect|value|list1|value|list2|value|list3|value|uselists|value";
   $MM_columnsStr = "field11text|',none,''|field12text|',none,''|field13text|',none,''|field14text|',none,''|field15text|',none,''|field16text|',none,''|field17text|',none,''|field18text|',none,''|field19text|',none,''|field20text|',none,''|11ftype|',none,''|12ftype|',none,''|13ftype|',none,''|14ftype|',none,''|15ftype|',none,''|16ftype|',none,''|17ftype|',none,''|18ftype|',none,''|19ftype|',none,''|20ftype|',none,''|11pub|none,none,NULL|12pub|none,none,NULL|13pub|none,none,NULL|14pub|none,none,NULL|15pub|none,none,NULL|16pub|none,none,NULL|17pub|none,none,NULL|18pub|none,none,NULL|19pub|none,none,NULL|20pub|none,none,NULL|field1text|',none,''|field2text|',none,''|field3text|',none,''|field4text|',none,''|field5text|',none,''|field6text|',none,''|field7text|',none,''|field8text|',none,''|field9text|',none,''|field10text|',none,''|1ftype|',none,''|2ftype|',none,''|3ftype|',none,''|4ftype|',none,''|5ftype|',none,''|6ftype|',none,''|7ftype|',none,''|8ftype|',none,''|9ftype|',none,''|10ftype|',none,''|name|',none,''|1pub|none,none,NULL|2pub|none,none,NULL|3pub|none,none,NULL|4pub|none,none,NULL|5pub|none,none,NULL|6pub|none,none,NULL|7pub|none,none,NULL|8pub|none,none,NULL|9pub|none,none,NULL|10pub|none,none,NULL|modidinput|none,none,NULL|modidresponse|none,none,NULL|sourceid|none,none,NULL|enteredby|none,none,NULL|useemail|none,none,NULL|mailto|',none,''|subject|',none,''|redirect|',none,''|list1|',none,''|list2|',none,''|list3|',none,''|uselists|none,1,0";
   
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
   
   }
   
$Recordset1__MMColParam = "8000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}

   $Recordset1=$dbcon->Execute("SELECT * FROM modfields WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
	$modlist=$dbcon->Execute("SELECT id, name FROM moduletext ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $modlist_numRows=0;
   $modlist__totalRows=$modlist->RecordCount();
	$enteredby=$dbcon->Execute("SELECT id, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $enteredby_numRows=0;
   $enteredby__totalRows=$enteredby->RecordCount();
   	$source=$dbcon->Execute("SELECT id, title FROM source ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $source_numRows=0;
   $source__totalRows=$source->RecordCount();
   
 
?><?php include ("header.php"); ?>

<h2> <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>Add<?php } else { ?>Update<?php }?>&nbsp;User Data Fields</h2>
<form name="form1" method="post" action="<?php echo $MM_editAction?>">
  <table width="95%" border="0" cellspacing="0" cellpadding="5" class="table">
    <tr class="intitle"> 
      <td>Name</td>
      <td> <input type="text" name="name" size="25" value="<?php echo $Recordset1->Fields("name")?>"> 
      </td>
      <td>id # <?php echo $Recordset1->Fields("id")?></td>
      <td>public</td>
    </tr>
    <tr> 
      <td class="name">Line 1</td>
      <td> <textarea name="field1text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field1text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="1ftype" value="1" <?php If (($Recordset1->Fields("1ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="1ftype" value="3" <?php If (($Recordset1->Fields("1ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text<br>
                    <input type="radio" name="1ftype" value="2" <?php If (($Recordset1->Fields("1ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="1ftype" value="0" <?php If (($Recordset1->Fields("1ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="1pub" type="checkbox" id="1pub" value="1" <?php If (($Recordset1->Fields("1pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 2</td>
      <td> <textarea name="field2text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field2text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="2ftype" value="1"  <?php If (($Recordset1->Fields("2ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="2ftype" value="3" <?php If (($Recordset1->Fields("2ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="2ftype" value="2" <?php If (($Recordset1->Fields("2ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox
              <input type="radio" name="2ftype" value="0" <?php If (($Recordset1->Fields("2ftype")) == "0") { echo "CHECKED";} ?> >
              None </td>
      <td><input name="2pub" type="checkbox" id="2pub" value="1" <?php If (($Recordset1->Fields("2pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr> 
      <td class="name">Line 3</td>
      <td> <textarea name="field3text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field3text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="3ftype" value="1"  <?php If (($Recordset1->Fields("3ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="3ftype" value="3" <?php If (($Recordset1->Fields("3ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="3ftype" value="2"  <?php If (($Recordset1->Fields("3ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox
              <input type="radio" name="3ftype" value="0" <?php If (($Recordset1->Fields("3ftype")) == "0") { echo "CHECKED";} ?> >
              None </td>
      <td><input name="3pub" type="checkbox" id="3pub" value="1" <?php If (($Recordset1->Fields("3pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 4</td>
      <td> <textarea name="field4text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field4text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="4ftype" value="1"  <?php If (($Recordset1->Fields("4ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="4ftype" value="3" <?php If (($Recordset1->Fields("4ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="4ftype" value="2"  <?php If (($Recordset1->Fields("4ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox
              <input type="radio" name="4ftype" value="0" <?php If (($Recordset1->Fields("4ftype")) == "0") { echo "CHECKED";} ?> >
              None </td>
      <td><input name="4pub" type="checkbox" id="4pub" value="1" <?php If (($Recordset1->Fields("4pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr> 
      <td class="name">Line 5</td>
      <td> <textarea name="field5text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field5text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="5ftype" value="1"  <?php If (($Recordset1->Fields("5ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="5ftype" value="3"  <?php If (($Recordset1->Fields("5ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="5ftype" value="2"  <?php If (($Recordset1->Fields("5ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox
              <input type="radio" name="5ftype" value="0" <?php If (($Recordset1->Fields("5ftype")) == "0") { echo "CHECKED";} ?> >
              None </td>
      <td><input name="5pub" type="checkbox" id="5pub" value="1" <?php If (($Recordset1->Fields("5pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 6</td>
      <td> <textarea name="field6text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field6text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="6ftype" value="1"  <?php If (($Recordset1->Fields("6ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="6ftype" value="3" <?php If (($Recordset1->Fields("6ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="6ftype" value="2" <?php If (($Recordset1->Fields("6ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="6ftype" value="0" <?php If (($Recordset1->Fields("6ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="6pub" type="checkbox" id="6pub" value="1" <?php If (($Recordset1->Fields("6pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr> 
      <td class="name">Line 7</td>
      <td> <textarea name="field7text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field7text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="7ftype" value="1"  <?php If (($Recordset1->Fields("7ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="7ftype" value="3" <?php If (($Recordset1->Fields("7ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="7ftype" value="2" <?php If (($Recordset1->Fields("7ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="7ftype" value="0" <?php If (($Recordset1->Fields("7ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="7pub" type="checkbox" id="7pub" value="1" <?php If (($Recordset1->Fields("7pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 8</td>
      <td> <textarea name="field8text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field8text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="8ftype" value="1"  <?php If (($Recordset1->Fields("8ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="8ftype" value="3" <?php If (($Recordset1->Fields("8ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="8ftype" value="2" <?php If (($Recordset1->Fields("8ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="8ftype" value="0" <?php If (($Recordset1->Fields("8ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="8pub" type="checkbox" id="8pub" value="1" <?php If (($Recordset1->Fields("8pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr> 
      <td class="name">Line 9</td>
      <td> <textarea name="field9text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field9text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="9ftype" value="1"  <?php If (($Recordset1->Fields("9ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="9ftype" value="3" <?php If (($Recordset1->Fields("9ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="9ftype" value="2" <?php If (($Recordset1->Fields("9ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="9ftype" value="0" <?php If (($Recordset1->Fields("9ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="9pub" type="checkbox" id="9pub" value="1" <?php If (($Recordset1->Fields("9pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 10</td>
      <td> <textarea name="field10text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field10text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="10ftype" value="1"  <?php If (($Recordset1->Fields("10ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="10ftype" value="3" <?php If (($Recordset1->Fields("10ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="10ftype" value="2" <?php If (($Recordset1->Fields("10ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox
              <input type="radio" name="10ftype" value="0" <?php If (($Recordset1->Fields("10ftype")) == "0") { echo "CHECKED";} ?> >
              None </td>
      <td><input name="10pub" type="checkbox" id="10pub" value="1" <?php If (($Recordset1->Fields("10pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 11</td>
      <td> <textarea name="field11text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field11text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="11ftype" value="1"  <?php If (($Recordset1->Fields("11ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box
              <input type="radio" name="11ftype" value="3" <?php If (($Recordset1->Fields("11ftype")) == "3") { echo "CHECKED";} ?> >
              Multi Line Text <br>
                    <input type="radio" name="11ftype" value="2" <?php If (($Recordset1->Fields("11ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox
              <input type="radio" name="11ftype" value="0" <?php If (($Recordset1->Fields("11ftype")) == "0") { echo "CHECKED";} ?> >
              None </td>
      <td><input name="11pub" type="checkbox" id="11pub" value="1" <?php If (($Recordset1->Fields("11pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 12</td>
      <td> <textarea name="field12text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field12text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="12ftype" value="1"  <?php If (($Recordset1->Fields("12ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="12ftype" value="3" <?php If (($Recordset1->Fields("12ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="12ftype" value="2" <?php If (($Recordset1->Fields("12ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="12ftype" value="0" <?php If (($Recordset1->Fields("12ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="12pub" type="checkbox" id="12pub" value="1" <?php If (($Recordset1->Fields("12pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 13</td>
      <td> <textarea name="field13text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field13text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="13ftype" value="1"  <?php If (($Recordset1->Fields("13ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="13ftype" value="3" <?php If (($Recordset1->Fields("13ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="13ftype" value="2" <?php If (($Recordset1->Fields("13ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="13ftype" value="0" <?php If (($Recordset1->Fields("13ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="13pub" type="checkbox" id="13pub" value="1" <?php If (($Recordset1->Fields("13pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 14</td>
      <td> <textarea name="field14text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field14text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="14ftype" value="1"  <?php If (($Recordset1->Fields("14ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="14ftype" value="3" <?php If (($Recordset1->Fields("14ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="14ftype" value="2" <?php If (($Recordset1->Fields("14ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox
              <input type="radio" name="14ftype" value="0" <?php If (($Recordset1->Fields("14ftype")) == "0") { echo "CHECKED";} ?> >
              None </td>
      <td><input name="14pub" type="checkbox" id="14pub" value="1" <?php If (($Recordset1->Fields("14pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 15</td>
      <td> <textarea name="field15text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field15text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="15ftype" value="1"  <?php If (($Recordset1->Fields("15ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="15ftype" value="3" <?php If (($Recordset1->Fields("15ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="15ftype" value="2" <?php If (($Recordset1->Fields("15ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="15ftype" value="0" <?php If (($Recordset1->Fields("15ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="15pub" type="checkbox" id="15pub" value="1" <?php If (($Recordset1->Fields("15pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 16</td>
      <td> <textarea name="field16text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field16text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="16ftype" value="1"  <?php If (($Recordset1->Fields("16ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="16ftype" value="3" <?php If (($Recordset1->Fields("16ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="16ftype" value="2" <?php If (($Recordset1->Fields("16ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="16ftype" value="0" <?php If (($Recordset1->Fields("16ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="16pub" type="checkbox" id="16pub" value="1" <?php If (($Recordset1->Fields("16pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 17</td>
      <td> <textarea name="field17text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field17text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="17ftype" value="1"  <?php If (($Recordset1->Fields("17ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="17ftype" value="3" <?php If (($Recordset1->Fields("17ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="17ftype" value="2" <?php If (($Recordset1->Fields("17ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox
              <input type="radio" name="17ftype" value="0" <?php If (($Recordset1->Fields("17ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="17pub" type="checkbox" id="17pub" value="1" <?php If (($Recordset1->Fields("17pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 18</td>
      <td> <textarea name="field18text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field18text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="18ftype" value="1"  <?php If (($Recordset1->Fields("18ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="18ftype" value="3" <?php If (($Recordset1->Fields("18ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="18ftype" value="2" <?php If (($Recordset1->Fields("18ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="18ftype" value="0" <?php If (($Recordset1->Fields("18ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="18pub" type="checkbox" id="18pub" value="1" <?php If (($Recordset1->Fields("18pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 19</td>
      <td> <textarea name="field19text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field19text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="19ftype" value="1"  <?php If (($Recordset1->Fields("19ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="19ftype" value="3" <?php If (($Recordset1->Fields("19ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="19ftype" value="2" <?php If (($Recordset1->Fields("19ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="19ftype" value="0" <?php If (($Recordset1->Fields("19ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="19pub" type="checkbox" id="19pub" value="1" <?php If (($Recordset1->Fields("19pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	<tr class="title"> 
      <td class="name">Line 20</td>
      <td> <textarea name="field20text" cols="25" rows="4" wrap="virtual"><?php echo $Recordset1->Fields("field20text")?></textarea> 
      </td>
      <td class="text"> <input type="radio" name="20ftype" value="1"  <?php If (($Recordset1->Fields("20ftype")) == "1") { echo "CHECKED";} ?> >
        Text Box 
        <input type="radio" name="20ftype" value="3" <?php If (($Recordset1->Fields("20ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="20ftype" value="2" <?php If (($Recordset1->Fields("20ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox 
              <input type="radio" name="20ftype" value="0" <?php If (($Recordset1->Fields("20ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="20pub" type="checkbox" id="20pub" value="1" <?php If (($Recordset1->Fields("20pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
	
  </table>
        
  <table width="95%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td class="name">Redirect URL</td>
      <td><input name="redirect" type="text" id="redirect" size="45" value="<?php echo $Recordset1->Fields("redirect")?>"></td>
    </tr>
    <tr> 
      <td class="name">Module ID (input)</td>
      <td><select name="modidinput">
	  <option value="">--</option>
	    <?php
  if ($modlist__totalRows > 0){
    $modlist__index=0;
    $modlist->MoveFirst();
    WHILE ($modlist__index < $modlist__totalRows){
?>
                  <OPTION VALUE="<?php echo  $modlist->Fields("id")?>"<?php if ($modlist->Fields("id")==$Recordset1->Fields("modidinput")) echo "SELECTED";?>> 
                  <?php echo  $modlist->Fields("name");?> </OPTION>
                  <?php
      $modlist->MoveNext();
      $modlist__index++;
    }
    $modlist__index=0;  
    $modlist->MoveFirst();
  }
?>
        </select>
                  </td>
    </tr>
    <tr> 
      <td class="name">Module ID (response)</td>
            <td><select name="modidresponse">
			<option value="">--</option>
                <?php
  if ($modlist__totalRows > 0){
    $modlist__index=0;
    $modlist->MoveFirst();
    WHILE ($modlist__index < $modlist__totalRows){
?>
                <option value="<?php echo  $modlist->Fields("id")?>"<?php if ($modlist->Fields("id")==$Recordset1->Fields("modidresponse")) echo "SELECTED";?>> 
                <?php echo  $modlist->Fields("name");?> </option>
                <?php
      $modlist->MoveNext();
      $modlist__index++;
    }
    $modlist__index=0;  
    $modlist->MoveFirst();
  }
?>
              </select> </td>
    </tr>
    <tr> 
      <td class="name">Source</td>
      <td class="name"><select name="sourceid">
        	    <?php
  if ($source__totalRows > 0){
    $source__index=0;
    $source->MoveFirst();
    WHILE ($source__index < $source__totalRows){
?>
                  <OPTION VALUE="<?php echo  $source->Fields("id")?>"<?php if ($source->Fields("id")==$Recordset1->Fields("sourceid")) echo "SELECTED";?>> 
                  <?php echo  $source->Fields("title");?> </OPTION>
                  <?php
      $source->MoveNext();
      $source__index++;
    }
    $source__index=0;  
    $source->MoveFirst();
  }
?>

		</select>
            </td>
    </tr>
    <tr> 
      <td class="name">Entered By</td>
      <td><select name="enteredbyid" id="enteredbyid">
	  	    <?php
  if ($enteredby__totalRows > 0){
    $enteredby__index=0;
    $enteredby->MoveFirst();
    WHILE ($enteredby__index < $enteredby__totalRows){
?>
                  <OPTION VALUE="<?php echo  $enteredby->Fields("id")?>"<?php if ($enteredby->Fields("id")==$Recordset1->Fields("enteredby")) echo "SELECTED";?>> 
                  <?php echo  $enteredby->Fields("name");?> </OPTION>
                  <?php
      $enteredby->MoveNext();
      $enteredby__index++;
    }
    $enteredby__index=0;  
    $enteredby->MoveFirst();
  }
?>

        </select>
                  </td>
    </tr><tr> 
                  <td class="name">use lists</td>
                  <td><input name="uselists" type="checkbox" id="uselists" value="1" <?php if ($Recordset1->Fields("uselists") == 1) { echo "CHECKED";} ?>></td>
                </tr><tr> 
                  <td  class="name">List # 1</td>
                  <td><select name="list1">
				<option value="">none</option> 
        	    <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$Recordset1->Fields("list1")) echo "SELECTED";?>> 
                  <?php echo  $list->Fields("name");?> </OPTION>
                  <?php
      $list->MoveNext();
      $list__index++;
    }
    $list__index=0;  
    $list->MoveFirst();
  }
?>

		</select></td>
                </tr>
                <tr> 
                  <td  class="name">List #2</td>
                  <td><select name="list2">
				<option value="">none</option> 
        	    <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$Recordset1->Fields("list2")) echo "SELECTED";?>> 
                  <?php echo  $list->Fields("name");?> </OPTION>
                  <?php
      $list->MoveNext();
      $list__index++;
    }
    $list__index=0;  
    $list->MoveFirst();
  }
?>

		</select></td>
                </tr>
                <tr> 
                  <td class="name">List #3</td>
                  <td><select name="list3">
				<option value="">none</option> 
        	    <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$Recordset1->Fields("list3")) echo "SELECTED";?>> 
                  <?php echo  $list->Fields("name");?> </OPTION>
                  <?php
      $list->MoveNext();
      $list__index++;
    }
    $list__index=0;  
    $list->MoveFirst();
  }
?>

		</select></td>
                </tr>
    <tr> 
      <td class="name">Use E-mail</td>
      <td><input name="useemail" type="checkbox" id="useemail" value="1" <?php If (($Recordset1->Fields("useemail")) == "1") { echo "CHECKED";} ?> ></td>
    </tr>
    <tr> 
      <td class="name">Mail to:</td>
      <td><input name="mailto" type="text" id="mailto" size="45" value="<?php echo $Recordset1->Fields("mailto")?>"></td>
    </tr>
    <tr> 
      <td class="name">E-mail Subject</td>
      <td><input name="subject" type="text" id="subject" size="45" value="<?php echo $Recordset1->Fields("subject")?>"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
            
              <p>
                   <input type="submit" name="Submit" value="Submit">
                <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
                <input type="hidden" name="MM_insert" value="true">
		<?php 
		}
		else { ?>
		<input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>"><input type="hidden" name="MM_update" value="true"><?php } ?>              </p>
            </form>  <form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="hidden" name="MM_delete" value="true">
	 <input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>">
	<input type="submit" name="Submit2" value="Delete"></form>
<?php
  $Recordset1->Close();
?><?php include("footer.php"); ?>


