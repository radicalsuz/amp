<?php
  require("Connections/freedomrising.php");
 if (isset($id)){ $modidselect=$dbcon->Execute("SELECT id from modules where userdatamodid=$id") or DIE($dbcon->ErrorMsg());
 
$modid=$modidselect->Fields("id");}
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
// *** Add/Update Record: set Variables

 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
        //Delete cached versions of output file
  $dbcon->Execute("DELETE FROM cachedata WHERE CACHEKEY LIKE '%modlist';")or DIE($dbcon->ErrorMsg());

   // $MM_editConnection = MM_freedomrising_STRING;

    $MM_editTable  = "modfields";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "modfields_list.php";
	$MM_fieldsStr = "field1text|value|field2text|value|field3text|value|field4text|value|field5text|value|field6text|value|field7text|value|field8text|value|field9text|value|field10text|value|1ftype|value|2ftype|value|3ftype|value|4ftype|value|5ftype|value|6ftype|value|7ftype|value|8ftype|value|9ftype|value|10ftype|value|name|value|1pub|value|2pub|value|3pub|value|4pub|value|5pub|value|6pub|value|7pub|value|8pub|value|9pub|value|10pub|value|modidinput|value|modidresponse|value|sourceid|value|enteredby|value|useemail|value|mailto|value|subject|value|redirect|value";
   $MM_columnsStr = "field1text|',none,''|field2text|',none,''|field3text|',none,''|field4text|',none,''|field5text|',none,''|field6text|',none,''|field7text|',none,''|field8text|',none,''|field9text|',none,''|field10text|',none,''|1ftype|',none,''|2ftype|',none,''|3ftype|',none,''|4ftype|',none,''|5ftype|',none,''|6ftype|',none,''|7ftype|',none,''|8ftype|',none,''|9ftype|',none,''|10ftype|',none,''|name|',none,''|1pub|none,none,NULL|2pub|none,none,NULL|3pub|none,none,NULL|4pub|none,none,NULL|5pub|none,none,NULL|6pub|none,none,NULL|7pub|none,none,NULL|8pub|none,none,NULL|9pub|none,none,NULL|10pub|none,none,NULL|modidinput|none,none,NULL|modidresponse|none,none,NULL|sourceid|none,none,NULL|enteredby|none,none,NULL|useemail|none,none,NULL|mailto|',none,''|subject|',none,''|redirect|',none,''";
  
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
   
$Recordset1__MMColParam = "8000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
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
      <td> <input type="text" name="field1text" size="25" value="<?php echo $Recordset1->Fields("field1text")?>"> 
      </td>
      <td class="text"> <input type="radio" name="1ftype" value="1" <?php If (($Recordset1->Fields("1ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box &nbsp; 
              <input type="radio" name="1ftype" value="3" <?php If (($Recordset1->Fields("1ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text<br>
                    <input type="radio" name="1ftype" value="2" <?php If (($Recordset1->Fields("1ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox &nbsp; 
              <input type="radio" name="1ftype" value="0" <?php If (($Recordset1->Fields("1ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="1pub" type="checkbox" id="1pub" value="1" <?php If (($Recordset1->Fields("1pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 2</td>
      <td> <input type="text" name="field2text" size="25" value="<?php echo $Recordset1->Fields("field2text")?>"> 
      </td>
      <td class="text"> <input type="radio" name="2ftype" value="1"  <?php If (($Recordset1->Fields("2ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box &nbsp; 
              <input type="radio" name="2ftype" value="3" <?php If (($Recordset1->Fields("2ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="2ftype" value="2" <?php If (($Recordset1->Fields("2ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox &nbsp;
              <input type="radio" name="2ftype" value="0" <?php If (($Recordset1->Fields("2ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="2pub" type="checkbox" id="2pub" value="1" <?php If (($Recordset1->Fields("2pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr> 
      <td class="name">Line 3</td>
      <td> <input type="text" name="field3text" size="25" value="<?php echo $Recordset1->Fields("field3text")?>"> 
      </td>
      <td class="text"> <input type="radio" name="3ftype" value="1"  <?php If (($Recordset1->Fields("3ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box &nbsp; 
              <input type="radio" name="3ftype" value="3" <?php If (($Recordset1->Fields("3ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="3ftype" value="2"  <?php If (($Recordset1->Fields("3ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox &nbsp; <input type="radio" name="3ftype" value="0" <?php If (($Recordset1->Fields("3ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="3pub" type="checkbox" id="3pub" value="1" <?php If (($Recordset1->Fields("3pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 4</td>
      <td> <input type="text" name="field4text" size="25" value="<?php echo $Recordset1->Fields("field4text")?>"> 
      </td>
      <td class="text"> <input type="radio" name="4ftype" value="1"  <?php If (($Recordset1->Fields("4ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box &nbsp; 
              <input type="radio" name="4ftype" value="3" <?php If (($Recordset1->Fields("4ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="4ftype" value="2"  <?php If (($Recordset1->Fields("4ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox &nbsp;
              <input type="radio" name="4ftype" value="0" <?php If (($Recordset1->Fields("4ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="4pub" type="checkbox" id="4pub" value="1" <?php If (($Recordset1->Fields("4pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr> 
      <td class="name">Line 5</td>
      <td> <input type="text" name="field5text" size="25" value="<?php echo $Recordset1->Fields("field5text")?>"> 
      </td>
            <td class="text"> <input type="radio" name="5ftype" value="1"  <?php If (($Recordset1->Fields("5ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box &nbsp; 
              <input type="radio" name="5ftype" value="3"  <?php If (($Recordset1->Fields("5ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="5ftype" value="2"  <?php If (($Recordset1->Fields("5ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox &nbsp;
<input type="radio" name="5ftype" value="0" <?php If (($Recordset1->Fields("5ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="5pub" type="checkbox" id="5pub" value="1" <?php If (($Recordset1->Fields("5pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 6</td>
      <td> <input type="text" name="field6text" size="25" value="<?php echo $Recordset1->Fields("field6text")?>"> 
      </td>
      <td class="text"> <input type="radio" name="6ftype" value="1"  <?php If (($Recordset1->Fields("6ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box &nbsp; 
              <input type="radio" name="6ftype" value="3" <?php If (($Recordset1->Fields("6ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="6ftype" value="2" <?php If (($Recordset1->Fields("6ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox &nbsp; 
              <input type="radio" name="6ftype" value="0" <?php If (($Recordset1->Fields("6ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="6pub" type="checkbox" id="6pub" value="1" <?php If (($Recordset1->Fields("6pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr> 
      <td class="name">Line 7</td>
      <td> <input type="text" name="field7text" size="25" value="<?php echo $Recordset1->Fields("field7text")?>"> 
      </td>
      <td class="text"> <input type="radio" name="7ftype" value="1"  <?php If (($Recordset1->Fields("7ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box &nbsp; 
              <input type="radio" name="7ftype" value="3" <?php If (($Recordset1->Fields("7ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="7ftype" value="2" <?php If (($Recordset1->Fields("7ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox &nbsp; 
              <input type="radio" name="7ftype" value="0" <?php If (($Recordset1->Fields("7ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="7pub" type="checkbox" id="7pub" value="1" <?php If (($Recordset1->Fields("7pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 8</td>
      <td> <input type="text" name="field8text" size="25" value="<?php echo $Recordset1->Fields("field8text")?>"> 
      </td>
      <td class="text"> <input type="radio" name="8ftype" value="1"  <?php If (($Recordset1->Fields("8ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box &nbsp; 
              <input type="radio" name="8ftype" value="3" <?php If (($Recordset1->Fields("8ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="8ftype" value="2" <?php If (($Recordset1->Fields("8ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox&nbsp;&nbsp; 
              <input type="radio" name="8ftype" value="0" <?php If (($Recordset1->Fields("8ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="8pub" type="checkbox" id="8pub" value="1" <?php If (($Recordset1->Fields("8pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr> 
      <td class="name">Line 9</td>
      <td> <input type="text" name="field9text" size="25" value="<?php echo $Recordset1->Fields("field9text")?>"> 
      </td>
      <td class="text"> <input type="radio" name="9ftype" value="1"  <?php If (($Recordset1->Fields("9ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box &nbsp; 
              <input type="radio" name="9ftype" value="3" <?php If (($Recordset1->Fields("9ftype")) == "3") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="9ftype" value="2" <?php If (($Recordset1->Fields("9ftype")) == "2") { echo "CHECKED";} ?> >
              Checkbox &nbsp;
              <input type="radio" name="9ftype" value="0" <?php If (($Recordset1->Fields("9ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="9pub" type="checkbox" id="9pub" value="1" <?php If (($Recordset1->Fields("9pub")) == "1") { echo "CHECKED";} ?>></td>
    </tr>
    <tr class="title"> 
      <td class="name">Line 10</td>
      <td> <input type="text" name="field10text" size="25" value="<?php echo $Recordset1->Fields("field10text")?>"> 
      </td>
      <td class="text"> <input type="radio" name="10ftype" value="1"  <?php If (($Recordset1->Fields("10ftype")) == "1") { echo "CHECKED";} ?> >
              Text Box&nbsp;&nbsp; 
              <input type="radio" name="10ftype" value="3" <?php If (($Recordset1->Fields("10ftype")) == "2") { echo "CHECKED";} ?> >
                    Multi Line Text <br>
                    <input type="radio" name="10ftype" value="2" <?php If (($Recordset1->Fields("10ftype")) == "3") { echo "CHECKED";} ?> >
              Checkbox &nbsp; 
              <input type="radio" name="10ftype" value="0" <?php If (($Recordset1->Fields("10ftype")) == "0") { echo "CHECKED";} ?> >
              None</td>
      <td><input name="10pub" type="checkbox" id="10pub" value="1" <?php If (($Recordset1->Fields("10pub")) == "1") { echo "CHECKED";} ?>></td>
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
	    <?php
  if ($modlist__totalRows > 0){
    $modlist__index=0;
    $modlist->MoveFirst();
    WHILE ($modlist__index < $modlist__totalRows){
?>
                  <OPTION VALUE="<?php echo  $modlist->Fields("id")?>"<?php if ($modlist->Fields("id")==$Recordset1->Fields("modidresponse")) echo "SELECTED";?>> 
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
      <td class="name">Source</td>
      <td><select name="sourceid">
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
      <td><select name="enteredby">
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


