<?php
  require("Connections/freedomrising.php");
    include("FCKeditor/fckeditor.php");
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
  // *** Update Record: set variables
  
 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
              //Delete cached versions of output file
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "moduletext";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "moduletext_list.php";
    $MM_fieldsStr = "title|value|subtitle|value|html|value|article|value|templateid|value|type|value|subtype|value|catagory|value|names|value|modid|value";
    $MM_columnsStr = "title|',none,''|subtitile|',none,''|html|none,1,0|test|',none,''|templateid|',none,''|type|',none,''|subtype|',none,''|catagory|',none,''|name|',none,''|modid|',none,''";
  
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
  
$type__MMColParam = "999999999999";
if (isset($HTTP_GET_VARS["id"]))
  {$type__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
   $type=$dbcon->Execute("SELECT * FROM moduletext WHERE id = " . ($type__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $type_numRows=0;
   $type__totalRows=$type->RecordCount();

   $templatelab=$dbcon->Execute("SELECT name, id FROM template ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
   $templatelab_numRows=0;
   $templatelab__totalRows=$templatelab->RecordCount();

   $typelab=$dbcon->Execute("SELECT id, type FROM articletype ORDER BY type ASC") or DIE($dbcon->ErrorMsg());
   $typelab_numRows=0;
   $typelab__totalRows=$typelab->RecordCount();
     
	$modlab=$dbcon->Execute("SELECT id, name FROM modules ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $modlab_numRows=0;
   $modlab__totalRows=$modlab->RecordCount();
?>

<?php include ("header.php"); ?>
<h2><?php echo helpme(""); ?>Add / Edit Module Text</h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST">
            <p><a href="module_nav_edit.php?id=<?php echo $HTTP_GET_VARS["id"]; ?>"> Edit Navigation Files</a>
   
           </p>   
        <table width="100%" border="0" align="center">
          <tr> 
            <td valign="top"> <div align="left"><b>Module ID#</b> </div></td>
            <td> <?php echo $type->Fields("id")?> </td>
          </tr>
          <tr> 
            <td valign="top" class="name"> <div align="left">Module Name</div></td>
            <td><input name="names" value="<?php echo htmlspecialchars( $type->Fields("name")) ?>" size="40" > 
            </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Title </td>
            <td><input name="title" value="<?php echo htmlspecialchars( $type->Fields("title")) ?>" size="55" > 
            </td>
          </tr>
          <tr> 
            <td valign="top" class="name"> <div align="left">Subtitle</div></td>
            <td> <input name="subtitle" size="55" value="<?php echo htmlspecialchars( $type->Fields("subtitile")) ?>"            > 
            </td>
          </tr>
<td colspan="2" valign="top" class="name">Full Text (not applicable 
              for offsite URLs)<br> 
              <?php 


if ($browser_mo) {?>
<script type="text/javascript">
  _editor_url = "htmlarea/";
  _editor_lang = "en";
</script>              <script type="text/javascript" src="htmlarea/htmlarea.js"></script> 
<script type="text/javascript">
      // WARNING: using this interface to load plugin
      // will _NOT_ work if plugins do not have the language
      // loaded by HTMLArea.

      // In other words, this function generates SCRIPT tags
      // that load the plugin and the language file, based on the
      // global variable HTMLArea.I18N.lang (defined in the lang file,
      // in our case "lang/en.js" loaded above).

      // If this lang file is not found the plugin will fail to
      // load correctly and nothing will work.

      HTMLArea.loadPlugin("TableOperations");
      HTMLArea.loadPlugin("SpellChecker");
      HTMLArea.loadPlugin("FullPage");
      HTMLArea.loadPlugin("CSS");
      HTMLArea.loadPlugin("ContextMenu");
</script>
<script type="text/javascript">
var editor = null;
function initEditor() {

  // create an editor for the "ta" textbox
  editor = new HTMLArea("articlemo");

  // register the FullPage plugin
  editor.registerPlugin(FullPage);

  // register the SpellChecker plugin
  editor.registerPlugin(TableOperations);

  // register the SpellChecker plugin
  //editor.registerPlugin(SpellChecker);
  setTimeout(function() {
    editor.generate();
  }, 500);
  return false;
}

</script> <textarea id = "articlemo" name="article" cols="80" rows="60" wrap="VIRTUAL" style="width:100%"><?php   if (($type->Fields("html")) != "1") 
   {$textvalue = nl2br($type->Fields("test"));}
   else 
   {$textvalue = $type->Fields("test");} 
   echo $textvalue;
   ?></textarea> <input name="html" type="hidden" value="1"> 
              <?php }
 
elseif (($browser_ie) && ($browser_win)) { 
   if (($type->Fields("html")) != "1") 
   {$textvalue = nl2br($type->Fields("test"));}
   else 
   {$textvalue = $type->Fields("test");}

$oFCKeditor = new FCKeditor ;
$oFCKeditor->Value = $textvalue  ;
$oFCKeditor->CreateFCKeditor( 'article', '500', 500 ) ;

?>
              <input name="html" type="hidden" value="1">
              <?php
} else {?>
              <input name="html" type="checkbox" value="1"  <?php If (($type->Fields("html")) == "1") { echo "CHECKED";} ?>>
              HTML Override (<font color="#FF0000">If you do not see a WYSIWYG 
              editor below please use a Mozilla browser<br> <textarea name="article" cols="65" rows="20" wrap="VIRTUAL"><?php
		$text2 = $type->Fields("test");
		if (($type->Fields("html")) == "1"){
		$text2 = str_replace("<BR>", "<BR>\r\n", $text2);} 
		 echo $text2; ?></textarea> 
              <?php } ?>
              <br> 
              <?php if  ($browser_ie) { echo "Note: When using Internet Explorer you can not input articles longer than 
        30,000 characters";}?>
            </td>
		  <tr>
            <td valign="top" class="name">Module</td>
            <td><select name="modid" id="modid">
                <option value="0">none</option>
                <?php
  if ($modlab__totalRows > 0){
    $modlab__index=0;
    $modlab->MoveFirst();
    WHILE ($modlab__index < $modlab__totalRows){
?>
                <option value="<?php echo  $modlab->Fields("id")?>"<?php if ($modlab->Fields("id")==$type->Fields("modid")) echo "SELECTED";?>> 
                <?php echo  $modlab->Fields("name");?> </option>
                <?php
      $modlab->MoveNext();
      $modlab__index++;
    }
    $modlab__index=0;  
    $modlab->MoveFirst();
  }
?>
              </select></td>
          </tr>
          <tr> 
            <td valign="top" class="name">Template </td>
            <td><select name="templateid" id="templateid">
                <option>none</option>
                <?php
  if ($templatelab__totalRows > 0){
    $templatelab__index=0;
    $templatelab->MoveFirst();
    WHILE ($templatelab__index < $templatelab__totalRows){
?>
                <OPTION VALUE="<?php echo  $templatelab->Fields("id")?>"<?php if ($templatelab->Fields("id")==$type->Fields("templateid")) echo "SELECTED";?>> 
                <?php echo  $templatelab->Fields("name");?> </OPTION>
                <?php
      $templatelab->MoveNext();
      $templatelab__index++;
    }
    $templatelab__index=0;  
    $templatelab->MoveFirst();
  }
?>
              </select></td>
          </tr>
          <tr> 
            <td valign="top" class="name">Type</td>
            <td><select name="type" id="type">
                <option value="1">none</option>
                <?php
  if ($typelab__totalRows > 0){
    $typelab__index=0;
    $typelab->MoveFirst();
    WHILE ($typelab__index < $typelab__totalRows){
?>
                <option value="<?php echo  $typelab->Fields("id")?>"<?php if ($typelab->Fields("id")==$type->Fields("type")) echo "SELECTED";?>> 
                <?php echo  $typelab->Fields("type");?> </option>
                <?php
      $typelab->MoveNext();
      $typelab__index++;
    }
    $typelab__index=0;  
    $typelab->MoveFirst();
  }
?>
              </select> </td>
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
<?php
  $type->Close();
?>
<?php
  $templatelab->Close();
?>
<?php
  $typelab->Close();
?>

<?php include("footer.php"); ?>

