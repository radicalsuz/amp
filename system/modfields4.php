<?php
  require("Connections/freedomrising.php");
 if (isset($id)){ $modidselect=$dbcon->Execute("SELECT id from modules where userdatamodid=$id") or DIE($dbcon->ErrorMsg());
 
 if (!$MM_listtable) {$MM_listtable= "lists";}
$modid=$modidselect->Fields("id");}
   	$list=$dbcon->Execute("SELECT id, name from $MM_listtable ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
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
$MM_fieldsStr ="field11text|value|field12text|value|field13text|value|field14text|value|field15text|value|field16text|value|field17text|value|field18text|value|field19text|value|field20text|value|11ftype|value|12ftype|value|13ftype|value|14ftype|value|15ftype|value|16ftype|value|17ftype|value|18ftype|value|19ftype|value|20ftype|value|11pub|value|12pub|value|13pub|value|14pub|value|15pub|value|16pub|value|17pub|value|18pub|value|19pub|value|20pub|value|field1text|value|field2text|value|field3text|value|field4text|value|field5text|value|field6text|value|field7text|value|field8text|value|field9text|value|field10text|value|1ftype|value|2ftype|value|3ftype|value|4ftype|value|5ftype|value|6ftype|value|7ftype|value|8ftype|value|9ftype|value|10ftype|value|name|value|1pub|value|2pub|value|3pub|value|4pub|value|5pub|value|6pub|value|7pub|value|8pub|value|9pub|value|10pub|value|modidinput|value|modidresponse|value|sourceid|value|enteredbyid|value|useemail|value|mailto|value|subject|value|redirect|value|list1|value|list2|value|list3|value|uselists|value|list4|value";
   $MM_columnsStr = "field11text|',none,''|field12text|',none,''|field13text|',none,''|field14text|',none,''|field15text|',none,''|field16text|',none,''|field17text|',none,''|field18text|',none,''|field19text|',none,''|field20text|',none,''|11ftype|',none,''|12ftype|',none,''|13ftype|',none,''|14ftype|',none,''|15ftype|',none,''|16ftype|',none,''|17ftype|',none,''|18ftype|',none,''|19ftype|',none,''|20ftype|',none,''|11pub|none,none,NULL|12pub|none,none,NULL|13pub|none,none,NULL|14pub|none,none,NULL|15pub|none,none,NULL|16pub|none,none,NULL|17pub|none,none,NULL|18pub|none,none,NULL|19pub|none,none,NULL|20pub|none,none,NULL|field1text|',none,''|field2text|',none,''|field3text|',none,''|field4text|',none,''|field5text|',none,''|field6text|',none,''|field7text|',none,''|field8text|',none,''|field9text|',none,''|field10text|',none,''|1ftype|',none,''|2ftype|',none,''|3ftype|',none,''|4ftype|',none,''|5ftype|',none,''|6ftype|',none,''|7ftype|',none,''|8ftype|',none,''|9ftype|',none,''|10ftype|',none,''|name|',none,''|1pub|none,none,NULL|2pub|none,none,NULL|3pub|none,none,NULL|4pub|none,none,NULL|5pub|none,none,NULL|6pub|none,none,NULL|7pub|none,none,NULL|8pub|none,none,NULL|9pub|none,none,NULL|10pub|none,none,NULL|modidinput|none,none,NULL|modidresponse|none,none,NULL|sourceid|none,none,NULL|enteredby|none,none,NULL|useemail|none,none,NULL|mailto|',none,''|subject|',none,''|redirect|',none,''|list1|',none,''|list2|',none,''|list3|',none,''|uselists|none,1,0|list4|',none,''";
   
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
   
   }
   
$R__MMColParam = "8000000";
if (isset($HTTP_GET_VARS["id"]))
  {$R__MMColParam = $HTTP_GET_VARS["id"];}

   $R=$dbcon->Execute("SELECT * FROM userdata_fields WHERE id = " . ($R__MMColParam) . "") or DIE($dbcon->ErrorMsg());
	$modlist=$dbcon->Execute("SELECT id, name FROM moduletext ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $modlist_numRows=0;
   $modlist__totalRows=$modlist->RecordCount();
	$enteredby=$dbcon->Execute("SELECT id, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $enteredby_numRows=0;
   $enteredby__totalRows=$enteredby->RecordCount();
   	$source=$dbcon->Execute("SELECT id, title FROM source ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $source_numRows=0;
   $source__totalRows=$source->RecordCount();
   
   
function fbl($l) {
	global $R;
?><a target="<?php echo $l ;?>"></a>
<table width="100%"  border="0" cellspacing="0" cellpadding="0" class=name> 
  <tr>
    <TD width=150><b><a href="#" onclick="change('<?php echo $l ;?>');"><?php echo $l ;?></a></b></td>
    <td colspan="2"><table width="100%" class=name><tr>    <td>
      <input name="enabled_<?php echo $l ;?>" type="checkbox"  value="1" <?php if ($R->Fields("enabled_".$l)) { echo "CHECKED";} ?>>
      enabled</td>
    <td>
      <input name="public_<?php echo $l ;?>" type="checkbox" value="1" <?php If ($R->Fields("public_".$l)) { echo "CHECKED";} ?>>public</td>
    <td>
      <input name="required_<?php echo $l ;?>" type="checkbox" value="1" <?php If ($R->Fields("required_".$l)) { echo "CHECKED";} ?>>required&nbsp;</td></tr></table></td>
    </tr></table>
	<div id="<?php echo $l ;?>" style="display: none ;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class=name bgcolor="#FFFFCC"> 
<tr>
    <td valign="top">
      Type:<br>
      <select name="type_<?php echo $l ;?>">
	  <?php if  ($R->Fields("type__".$l)) { echo "<option>".$R->Fields("type__".$l)."</option>"; } ?>
	  <option>text</option>
	  <option>checkbox</option>
	  <option>textarea</option>
	  <option>header</option>
	  <option>hidden</option>
	  <option>select</option>
	  <option>radio</option>
	  <option>date</option>
	  <option>file</option>
	  <option>password</option>
	  <option>button</option>
	  <option>image</option>
	  <option>reset</option>
	  <option>submit</option>
	  <option>xbutton</option>
	  <option>advcheckbox</option>
	  <option>autocomplete</option>
	  <option>hierselect</option>
	  <option>html</option>
	  <option>link</option>
	  <option>static</option>
	  
      </select>      <br></td>
    <td colspan="2" valign="top">Label:<br> 
      <textarea name="label_<?php echo $l ;?>" cols="40" rows="2" wrap="virtual"><?php echo $R->Fields("label_".$l)?></textarea></td>
    </tr>
  <tr>
    <td valign="top">Region:<br></td>
    <td>Defualt Value: <br>
      <input name="values_<?php echo $l ;?>" type="text" size="25" value="<?php echo $R->Fields("values_".$l); ?>"></td>
    <td valign="top">Field Size:<br>
      <input name="size_<?php echo $l ;?>" type="text" size="5" value="<?php echo $R->Fields("size_".$l); ?>"> </td>
  </tr>
</table>
</div>
<?php
   
   
}

 
?><?php include ("header.php"); ?>
<script type="text/javascript">
function change(which) {
	document.getElementById(which).style.display = 'block';
	
}
</script>

<h2> <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>Add<?php } else { ?>Update<?php }?>&nbsp;User Data Fields</h2>
<form name="form1" method="post" action="<?php echo $MM_editAction?>">

  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="table">
    <tr class="intitle"> 
      <td>Name</td>
      <td> <input type="text" name="name" size="25" value="<?php echo $R->Fields("name")?>"> 
      </td>
      <td>id # <?php echo $R->Fields("id")?></td>
      <td>public</td>
    </tr>
	
  </table>
        
  <table width="95%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td class="name">Redirect URL</td>
      <td><input name="redirect" type="text" id="redirect" size="45" value="<?php echo $R->Fields("redirect")?>"></td>
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
                  <OPTION VALUE="<?php echo  $modlist->Fields("id")?>"<?php if ($modlist->Fields("id")==$R->Fields("modidinput")) echo "SELECTED";?>> 
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
                <option value="<?php echo  $modlist->Fields("id")?>"<?php if ($modlist->Fields("id")==$R->Fields("modidresponse")) echo "SELECTED";?>> 
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
                  <OPTION VALUE="<?php echo  $source->Fields("id")?>"<?php if ($source->Fields("id")==$R->Fields("sourceid")) echo "SELECTED";?>> 
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
                  <OPTION VALUE="<?php echo  $enteredby->Fields("id")?>"<?php if ($enteredby->Fields("id")==$R->Fields("enteredby")) echo "SELECTED";?>> 
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
                  <td><input name="uselists" type="checkbox" id="uselists" value="1" <?php if ($R->Fields("uselists") == 1) { echo "CHECKED";} ?>></td>
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
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$R->Fields("list1")) echo "SELECTED";?>> 
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
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$R->Fields("list2")) echo "SELECTED";?>> 
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
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$R->Fields("list3")) echo "SELECTED";?>> 
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
                  <td class="name">List #4</td>
                  <td><select name="list4">
				<option value="">none</option> 
        	    <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$R->Fields("list4")) echo "SELECTED";?>> 
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
      <td><input name="useemail" type="checkbox" id="useemail" value="1" <?php If (($R->Fields("useemail")) == "1") { echo "CHECKED";} ?> ></td>
    </tr>
    <tr> 
      <td class="name">Mail to:</td>
      <td><input name="mailto" type="text" id="mailto" size="45" value="<?php echo $R->Fields("mailto")?>"></td>
    </tr>
    <tr> 
      <td class="name">E-mail Subject</td>
      <td><input name="subject" type="text" id="subject" size="45" value="<?php echo $R->Fields("subject")?>"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
<?php 
fbl('Title ');
fbl('First_Name');
fbl('Last_Name');
fbl('MI');
fbl('Suffix');
fbl('Company');
fbl('occupation');
fbl('Notes');
fbl('Email');
fbl('Phone');
fbl('Cell_Phone');
fbl('Phone_Provider');
fbl('Work_Phone');
fbl('Pager');
fbl('Work_Fax');
fbl('Home_Fax');
fbl('Web_Page');
fbl('Street');
fbl('Street_2');
fbl('Street_3');
fbl('City');
fbl('State');
fbl('Zip');
fbl('Country');
fbl('region');
$i=1;
while ($i <= 40) {
fbl('custom'.$i);
$i++;
}



?>            
              <p>
                   <input type="submit" name="Submit" value="Submit">
                <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
                <input type="hidden" name="MM_insert" value="true">
		<?php 
		}
		else { ?>
		<input type="hidden" name="MM_recordId" value="<?php echo $R->Fields("id") ?>"><input type="hidden" name="MM_update" value="true"><?php } ?>              </p>
      </form>  <form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="hidden" name="MM_delete" value="true">
	 <input type="hidden" name="MM_recordId" value="<?php echo $R->Fields("id") ?>">
	<input type="submit" name="Submit2" value="Delete"></form>
<?php include("footer.php"); ?>


