<?php
$mod_name='content';
require("Connections/freedomrising.php");
include("FCKeditor/fckeditor.php");

if (isset($preview)) {header ("Location: ../article.php?id=$id&preview=1");}
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
  
   if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
   //Delete cached versions of output file
 
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "articles";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "article_list.php?class=2";
    $MM_fieldsStr = "type|value|subtype|value|catagory|value|uselink|value|publish|value|title|value|subtitle|value|html|value|article|value|textfield|value|author|value|linktext|value|date|value|usedate|value|doc|value|radiobutton|value|link|value|linkuse|value|new|value|actionitem|value|actionlink|value|piccap|value|picture|value|usepict|value|morelink|value|usemore|value|pageorder|value|class|value|source|value|contact|value|alignment|value|alttag|value|state|value|pselection|value";
    $MM_columnsStr = "type|none,none,NULL|subtype|none,none,NULL|catagory|none,none,NULL|uselink|none,1,0|publish|none,1,0|title|',none,''|subtitile|',none,''|html|none,1,0|test|',none,''|shortdesc|',none,''|author|',none,''|linktext|',none,''|date|',none,NULL|usedate|none,1,0|doc|',none,''|doctype|',none,''|link|',none,''|linkover|none,1,0|new|none,1,0|actionitem|none,1,0|actionlink|none,none,NULL|piccap|',none,''|picture|',none,''|picuse|none,none,NULL|morelink|',none,''|usemore|none,1,0|pageorder|none,none,NULL|class|none,none,NULL|source|',none,''|contact|',none,''|alignment|',none,''|alttag|',none,''|state|none,none,NULL|pselection|',none,''";
  
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
  ?><?php
  
$type__MMColParam = "90000000000";
if (isset($HTTP_GET_VARS["id"]))
  {$type__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
   $type=$dbcon->Execute("SELECT * FROM articles WHERE id = " . ($type__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $type_numRows=0;
   $type__totalRows=$type->RecordCount();
    $state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
?><?php
   $typelab=$dbcon->Execute("SELECT id, type FROM articletype ORDER BY type ASC") or DIE($dbcon->ErrorMsg());
   $typelab_numRows=0;
   $typelab__totalRows=$typelab->RecordCount();
?>
<?php
   $class=$dbcon->Execute("SELECT id, class FROM class ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
   $class_numRows=0;
   $class__totalRows=$class->RecordCount();
?>
<?php
   $action=$dbcon->Execute("SELECT * FROM sendfax ORDER BY subject ASC") or DIE($dbcon->ErrorMsg());
   $action_numRows=0;
   $action__totalRows=$action->RecordCount();
?>
<?php include ("header.php"); ?>

<form ACTION="<?php echo $MM_editAction ?>" METHOD="POST">
             
              
        <table width="100%" border="0" align="center">
          <tr class="banner"> 
            <td colspan="2" valign="top">Add/Edit Front Page Content</td>
          </tr>
          <tr> 
            <td colspan="2" valign="top">ID #<?php echo $type->Fields("id")?> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"> 
              <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> 
              <input type="submit" name="preview" value="Preview" onclick="return confirmSubmit('Please save this record first or all changes will be lost\nPress OK to continue or CANCEL to return and save you work')"> 
              <input type="hidden" name="class" value="2"> <input type="hidden" name="type" value="1"> 
              <input type="hidden" name="subtype" value="1"> <input type="hidden" name="catagory" value="1"></td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("header"); ?>Header</td>
          </tr>
          <tr> 
            <td valign="top"> <span align="left" class="name">Title </span
			><span class="red">*</span></td>
            <td> <textarea name="title" cols="45" rows="3" wrap="VIRTUAL"><?php echo htmlspecialchars( $type->Fields("title")) ?></textarea> 
            </td>
          </tr>
          <tr> 
            <td valign="top"> <span align="left" class="name">Subtitle</span></td>
            <td> <textarea name="subtitle" cols="45" rows="3" wrap="VIRTUAL"><?php echo htmlspecialchars( $type->Fields("subtitile")) ?></textarea> 
            </td>
          </tr>
          <tr> 
            <td valign="top"> <span align="left" class="name">Author</span></td>
            <td> <input name="author" size="50" value="<?php echo $type->Fields("author")?>" > 
            </td>
          </tr>
          <tr> 
            <td valign="top"> <span align="left" class="name">Source</span></td>
            <td><input name="source" size="50" value="<?php echo $type->Fields("source")?>" > 
            </td>
          </tr>
          <tr> 
            <td valign="top"><span align="left" class="name">Date</span><span class="red">*</span><br> 
            </td>
            <td valign="top" class="text"> <input type="text" name="date" size="25" value="<?php echo $type->Fields("date")?>">
              (2002-12-30)<br> <input <?php If (($type->Fields("usedate")) == "1") { echo "CHECKED";} ?> type="checkbox" name="usedate" value="1">
              REAL DATE</td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("display"); ?>Display 
              and Publishing</td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><input <?php If (($type->Fields("publish")) == "1") { echo "CHECKED";} If (($type->Fields("id")) == $NULL) { echo "CHECKED";}?>  type="checkbox" name="publish" value="1"> 
              <font color="#990000" size="3"><strong>PUBLISH </strong></font></td>
          </tr>
          <tr> 
            <td valign="top"><span align="left" class="name">&quot;More&quot; 
              Link</span></td>
            <td> <input name="morelink" type="text" id="morelink" size="50" value="<?php echo $type->Fields("morelink")?>" > 
            </td>
          </tr>
          <tr> 
            <td valign="top">&nbsp;</td>
            <td> <input name="usemore" type="checkbox" id="usemore" <?php If (($type->Fields("usemore")) == "1") { echo "CHECKED";} ?>> 
              <span align="left" class="name">USE LINK </span></td>
          </tr>
          <tr> 
            <td valign="top"><span align="left" class="name">Front Page Order</span></td>
            <td><input name="pageorder" type="text" id="pageorder" size="5" value="<?php echo $type->Fields("pageorder")?>" ></td>
          </tr>
          <?php
		  
		  ###### you have to reinsert the php into the html to makes work again ####
		   // If (($type->Fields("new")) == "1") { echo "CHECKED";} ?>
          <!-- <tr> 
            <td colspan="2" valign="top" class="text"><input  type="checkbox" name="new">
              LISTED AS NEW
			   </td>
          </tr> -->
          <?php //If (($type->Fields("fplink")) == "1") { echo "CHECKED";} ?>
          <!--  <tr> 
            <td colspan="2" valign="top" class="text"><input  type="checkbox" name="uselink2" value="1">
              SHOW LINK ABOVE SIDEBAR</td>
          </tr> -->
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("content"); ?>Content 
              Body</td>
          <tr> 
             <td colspan="2" valign="top" class="name">Full Text (not applicable 
              for offsite URLs)<br> 
              <?php echo WYSIWYG($type->Fields("test"),$type->Fields("html")); ?>
            </td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("image"); ?>Image (to 
              appear on front page and in first paragraph of article)</td>
          </tr>
          <tr> 
            <td valign="top" class="name">Image Filename</td>
            <td> <input type="text" name="picture" size="50" value="<?php echo $type->Fields("picture")?>"> 
            </td>
          </tr>
          <tr class="text"> 
            <td valign="top"><div align="right"></div></td>
            <td><p> &nbsp;<a href="imgdir.php" target="_blank">view images</a> 
                | <a href="imgup.php" target="_blank">upload image</a><br>
                <input <?php If (($type->Fields("picuse")) == "1") { echo "CHECKED";} ?> type="checkbox" name="usepict" value="1">
                USE THIS IMAGE<br>
              </p></td>
          </tr>
          <tr> 
            <td valign="top" class="name">Image Selection</td>
            <td class="text"> <input type="radio" name="pselection" value="original" <?php if ($type->Fields("pselection") == "original") echo("CHECKED");?>>
              Original 
              <input name="pselection" type="radio" value="pic" <?php if ($type->Fields("pselection") == "pic") echo("CHECKED");?>>
              Optimized </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Alignment</td>
            <td class="text"> <input type="radio" name="alignment" value="left" <?php if ($type->Fields("alignment") == "left") echo("CHECKED");?>>
              Left 
              <input name="alignment" type="radio" value="right" <?php if ($type->Fields("alignment") == "right") echo("CHECKED");?>>
              Right</td>
          </tr>
          <tr> 
            <td valign="top" class="name">Image Caption</td>
            <td> <input type="textarea" name="piccap" size="50" value="<?php echo $type->Fields("piccap")?>"> 
            </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Alt Tag<br>
              (short!)</td>
            <td> <input name="alttag" type="textarea" id="alttag" value="<?php echo $type->Fields("alttag")?>" size="50"> 
            </td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("region"); ?>Regional 
              Content</td>
          </tr>
          <tr> 
            <td class="name"> State</td>
            <td><select name="state">
                <option value="">Select State</option>
                <?php    if ($state__totalRows > 0){
    $state__index=0;
    $state->MoveFirst();
    WHILE ($state__index < $state__totalRows){
?>
                <option value="<?php echo  $state->Fields("id")?>" <?php if ($state->Fields("id")==$type->Fields("state")) echo "SELECTED";?>> 
                <?php echo  $state->Fields("statename");?> </option>
                <?php
      $state->MoveNext();
      $state__index++;
    }
    $state__index=0;  
    $state->MoveFirst();
  } ?>
              </select></td>
          </tr>
          <td colspan="2" valign="top"><input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"> 
            <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> 
            <input type="submit" name="preview" value="Preview" onclick="return confirmSubmit('Please save this record first or all changes will be lost\nPress OK to continue or CANCEL to return and save you work')"></td>
          </tr>
        </table>
              
	<input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
     
	 
	  </form>


<?php include ("footer.php"); ?>