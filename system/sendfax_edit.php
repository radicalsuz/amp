<?php
$modid=21;
$mod_name = 'actions';
  require("Connections/freedomrising.php");
  
  if (!$MM_listtable) {$MM_listtable= "lists";}
  // *** Update Record: set variables
 
   if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) ) {
  

$enddate =  DateConvertIn($enddate);
    $MM_editTable  = "action_text";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "sendfax_list.php";
	  $MM_fieldsStr ="title|value|introtext|value|shortdesc|value|subject|value|text|value|firstname|value|lastname|value|prefix|value|position|value|actiontype|value|email|value|fax|value|uselist|value|list1|value|list2|value|list3|value|list4|value|enddate|value|tellfriend|value|tf_subject|value|tf_text|value|thankyou_title|value|thankyou_text|value|bcc|value|faxaccount|value|faxsubject|value";
  $MM_columnsStr =  "title|',none,''|introtext|',none,''|shortdesc|',none,''|subject|',none,''|text|',none,''|firstname|',none,''|lastname|',none,''|prefix|',none,''|position|',none,''|actiontype|',none,''|email|',none,''|fax|',none,''|uselist|',none,''|list1|',none,''|list2|',none,''|list3|',none,''|list4|',none,''|enddate|',none,''|tellfriend|',none,''|tf_subject|',none,''|tf_text|',none,''|thankyou_title|',none,''|thankyou_text|',none,''|bcc|',none,''|faxaccount|',none,''|faxsubject|',none,''";
  
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   } 
   $fax__MMColParam = "90000000";
if ($_GET["id"])
  {$fax__MMColParam = $_GET["id"];}
   $fax=$dbcon->Execute("SELECT * FROM action_text WHERE id = " . ($fax__MMColParam) . "") or DIE($dbcon->ErrorMsg());
	$list=$dbcon->Execute("SELECT id, name from $MM_listtable ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $list_numRows=0;
   $list__totalRows=$list->RecordCount();
?><?php include("header.php"); ?>
<h2 >Add/Edit Web Action</h2>
<form ACTION="<?php echo $PHP_SELF?>" METHOD="POST" name="Form1">
        <table width="100%" border="0" align="center" class="name">
          <tr class="intitle"> 
            <td colspan="2"  valign="top">Action Introduction</td>
          </tr>
          <tr> 
            <td  valign="top">Action Title</td>
            <td><input type="text" name="title" size="50" value="<?php echo $fax->Fields("title")?>"></td>
          </tr>
          <tr> 
            <td  valign="top">Introduction Text</td>
            <td><textarea name="introtext" cols="50" rows="15" wrap="VIRTUAL"><?php echo $fax->Fields("introtext")?></textarea></td>
          </tr>
          <tr> 
            <td  valign="top">Short Description</td>
            <td><textarea name="shortdesc" cols="50" rows="5" wrap="VIRTUAL"><?php echo $fax->Fields("shortdesc")?></textarea></td>
          </tr>
          <tr> 
            <td ></td>
            <td></td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2" >Email Action </td>
          </tr>
          <tr> 
            <td > Message Subject</td>
            <td> <input type="text" name="subject" size="50" value="<?php echo $fax->Fields("subject")?>"> 
            </td>
          </tr>
          <tr> 
            <td  valign="top"> <p align="left">Message Text</p>
              <p align="left">&nbsp;</p></td>
            <td> <textarea name="text" cols="50" rows="20" wrap="VIRTUAL"><?php echo $fax->Fields("text")?></textarea> 
            </td>
          </tr>
		  <tr> 
            <td > Action Enddate</td>
            <td> <input type="text" name="enddate" size="50" value="<?php echo DateConvertOut($fax->Fields("enddate"))?>"> 
            </td>
          </tr>
          <tr> 
            <td  valign="top"></td>
            <td></td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2"  valign="top">Target Info</td>
          </tr>
          <tr> 
            <td >Target Prefix</td>
            <td> <input type="text" name="prefix" value=<?php echo $fax->Fields("prefix")?>> 
            </td>
          </tr>
          <tr> 
            <td > Target First Name</td>
            <td> <input type="text" name="firstname" size="50" value="<?php echo $fax->Fields("firstname")?>"> 
            </td>
          </tr>
          <tr> 
            <td > Target Last Name</td>
            <td> <input type="text" name="lastname" size="50" value="<?php echo $fax->Fields("lastname")?>"> 
            </td>
          </tr>
          <tr> 
            <td > Target Position</td>
            <td> <input type="text" name="position" size="50" value="<?php echo $fax->Fields("position")?>"> 
            </td>
          </tr>
          <tr> 
            <td > Target email address</td>
            <td> <input type="text" name="email" size="50" value="<?php echo $fax->Fields("email")?>"> 
            </td>
          </tr>
          <tr> 
            <td > fax number (as email address)</td>
            <td> <input type="text" name="fax" size="50" value="<?php echo $fax->Fields("fax")?>"> 
            </td>
          </tr>
		  <tr> 
            <td >Fax Account: email from</td>
            <td><input type="text" name="faxaccount" size="50" value="<?php echo $fax->Fields("faxaccount")?>"></td>
          </tr>
          <tr> 
            <td >Fax Account: email subject</td>
            <td><input type="text" name="faxsubject" size="50" value="<?php echo $fax->Fields("faxsubject")?>"></td>
          </tr>
          <tr> 
            <td >BCC Email Address</td>
            <td><input type="text" name="bcc" size="50" value="<?php echo $fax->Fields("bcc")?>"></td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2" >Lists</td>
          </tr>
          <tr> 
            <td >Use Lists</td>
            <td ><input name="uselist" type="checkbox"  value="1"  <?php if  ($fax->Fields("uselist")) {echo "checked"; } ?>></td>
          </tr>
          <tr> 
            <td >List 1</td>
            <td ><select name="list1">
				<option value="">none</option> 
        	    <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$fax->Fields("list1")) echo "SELECTED";?>> 
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
            <td >List 2</td>
            <td ><select name="list2">
				<option value="">none</option> 
        	    <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$fax->Fields("list2")) echo "SELECTED";?>> 
                  <?php echo  $list->Fields("name");?> </OPTION>
                  <?php
      $list->MoveNext();
      $list__index++;
    }
    $list__index=0;  
    $list->MoveFirst();
  }
?>

		</select>
            </td>
          </tr>
          <tr> 
            <td >List 3</td>
            <td ><select name="list3">
				<option value="">none</option> 
        	    <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$fax->Fields("list3")) echo "SELECTED";?>> 
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
            <td >List 4</td>
            <td ><select name="list4">
				<option value="">none</option> 
        	    <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                  <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$fax->Fields("list4")) echo "SELECTED";?>> 
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
          <tr class="intitle"> 
            <td colspan="2" >Tell A Friend</td>
          </tr>
          <tr> 
            <td >Use Tell a Friend</td>
            <td ><input name="tellfriend" type="checkbox" id="tellfriend" value="1" <?php if  ($fax->Fields("tellfriend")) {echo "checked"; } ?>></td>
          </tr>
          <tr> 
            <td >Tell a Friend Subject</td>
            <td ><input name="tf_subject" type="text"  value="<?php echo $fax->Fields("tf_subject")?>" size="50"></td>
          </tr>
          <tr> 
            <td >Tell a Friend Message Text</td>
            <td ><textarea name="tf_text" cols="50" rows="5" wrap="VIRTUAL"><?php echo $fax->Fields("tf_text")?></textarea></td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2" >Thank You Page</td>
          </tr>
          <tr> 
            <td >Thank You Title</td>
            <td ><input name="thankyou_title" type="text"  value="<?php echo $fax->Fields("thankyou_title")?>" size="50"></td>
          </tr>
          <tr> 
            <td >Thank You Text</td>
            <td ><textarea name="thankyou_text" cols="50" rows="5" wrap="VIRTUAL"><?php echo $fax->Fields("thankyou_text")?></textarea></td>
          </tr>
          <tr class="intitle"> 
            <td > Congress Merge Action</td>
            <td><input type="radio" name="actiontype" value="Congress Merge" <?php If (($fax->Fields("actiontype")) == "Congress Merge") { echo "CHECKED";} ?>> 
            </td>
          </tr>
          <tr> 
            <td ></td>
            <td></td>
          </tr>
          <tr> 
            <td >Action issue code</td>
            <td> <input name="issuecode" type="text" id="issuecode" value="<?php echo $fax->Fields("issuecode")?>" size="50"> 
            </td>
          </tr>
          <tr> 
            <td >Site Code</td>
            <td> <input name="site" type="text" id="site" value="<?php if ($fax->Fields("site") == NULL) {echo "$CM_site";} else {   echo $fax->Fields("site");}?>" size="50"> 
            </td>
          </tr>
          <tr> 
            <td >&nbsp;</td>
            <td >&nbsp;</td>
          </tr>
          <tr> 
            <td >&nbsp;</td>
            <td >&nbsp;</td>
          </tr>
        </table>
  <p><input name="submit" type="submit" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
                <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
                <input type="hidden" name="MM_insert" value="true">
                <?php 
		}
		else { ?>
                <input type="hidden" name="MM_update" value="true">
                <?php } ?>
                <input type="hidden" name="MM_recordId" value="<?php echo $_GET["id"]; ?>">
</form>
<?php include("footer.php"); ?><?php
  $fax->Close();
?>
