<?php
  require("Connections/freedomrising.php");
  include("Connections/menu.class.php");
//add spaw controls
if (!ereg('/$', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/';
else
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
  echo $_root ;

define('DR', $_root);
unset($_root);
$spaw_root = DR.'newmas/system/spaw/';

// include the control file
include $spaw_root.'spaw_control.class.php';


// create Menu
$obj = new Menu;
?><?php
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
 // $dbcon->Execute("DELETE FROM cachedata WHERE CACHEKEY LIKE '%article.php%';")or DIE($dbcon->ErrorMsg());
   // $dbcon->Execute("DELETE FROM cachedata WHERE CACHEKEY LIKE '%index%';")or DIE($dbcon->ErrorMsg());    
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "articles";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "article_list.php";
	if (isset($MM_insert)) {$datecreated = date("y-n-j");;
							$enteredby = $ID;}
								
	$date =  DateConvertIn($date);
     // $dbcon->Execute("UNLOCK TABLES") or DIE($dbcon->ErrorMsg());
	$MM_fieldsStr = "type|value|subtype|value|select3|value|uselink|value|publish|value|title|value|subtitle|value|html|value|article|value|textfield|value|author|value|linktext|value|date|value|usedate|value|doc|value|radiobutton|value|link|value|linkuse|value|new|value|actionitem|value|actionlink|value|piccap|value|picture|value|usepict|value|morelink|value|usemore|value|pageorder|value|class|value|source|value|contact|value|alignment|value|alttag|value|state|value|pselection|value|fplink|value|ID|value|enteredby|value|datecreated|value|sourceurl|value|notes|value";
    $MM_columnsStr = "type|none,none,NULL|subtype|none,none,NULL|catagory|none,none,NULL|uselink|none,1,0|publish|none,1,0|title|',none,''|subtitile|',none,''|html|none,1,0|test|',none,''|shortdesc|',none,''|author|',none,''|linktext|',none,''|date|',none,NULL|usedate|none,1,0|doc|',none,''|doctype|',none,''|link|',none,''|linkover|none,1,0|new|none,1,0|actionitem|none,1,0|actionlink|none,none,NULL|piccap|',none,''|picture|',none,''|picuse|none,none,NULL|morelink|',none,''|usemore|none,1,0|pageorder|none,none,NULL|class|none,none,NULL|source|',none,''|contact|',none,''|alignment|',none,''|alttag|',none,''|state|none,none,NULL|pselection|',none,''|fplink|',none,''|updatedby|',none,''|enteredby|',none,''|datecreated|',none,''|sourceurl|',none,''|notes|',none,''";
  
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
  ?><?php
  
$type__MMColParam = "90000000000";
if (isset($HTTP_GET_VARS["id"]))
  {$type__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
// $dbcon->Execute("LOCK TABLES articles WRITE, states WRITE, articletype WRITE, articlesubtype WRITE, catagory WRITE, class WRITE, sendfax WRITE") or DIE($dbcon->ErrorMsg());
   $type=$dbcon->Execute("SELECT * FROM articles WHERE id = " . ($type__MMColParam) . "") or DIE($dbcon->ErrorMsg());
    
   $type_numRows=0;
   $type__totalRows=$type->RecordCount();
    $state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
?><?php
if (isset($id)) {$typevar=$type->Fields("type");}
else {$typevar=1;}
   $typelab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$typevar."") or DIE($dbcon->ErrorMsg());
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
      <td colspan="2" valign="top"><?php echo helpme("main"); ?>Add/Edit Content</td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="name">ID #<?php echo $type->Fields("id")?> 
      </td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="name"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="name">
          <tr> 
            <td width="25%">Date Created</td>
            <td width="25%"><div align="left"><?php echo DoDateTime($type->Fields("datecreated"),("n/j/y"))?></div></td>
            <td width="25%">Date Modified</td>
            <td width="25%"> <div align="left">
                <?php if ($type->Fields("updated")!= NULL) { echo DoTimeStamp($type->Fields("updated"),("n/j/y"));}?>
              </div></td>
          </tr>
          <tr> 
            <td>Created By</td>
            <td><div align="left"> 
                <?php if ($type->Fields("enteredby")!= NULL) {
	$users=$dbcon->Execute("SELECT name FROM users where id =".$type->Fields("enteredby")."") or DIE($dbcon->ErrorMsg());
	echo $users->Fields("name");}?>
              </div></td>
            <td>Last Modified BY</td>
            <td><div align="left"> 
                <?php if ($type->Fields("updatedby")!= NULL) {
	$users=$dbcon->Execute("SELECT name FROM users where id =".$type->Fields("updatedby")."") or DIE($dbcon->ErrorMsg());
	echo $users->Fields("name");}?>
              </div></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top"><input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"> 
        <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> 
        <input type="submit" name="preview" value="Preview" onclick="return confirmSubmit('Please save this record first or all changes will be lost\nPress OK to continue or CANCEL to return and save you work')"></td>
    </tr>
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("header"); ?>Header&nbsp;&nbsp; </td>
    </tr>
    <tr> 
      <td valign="top"> <span align="left" class="name">Title </span
			><span class="red">*</span></td>
      <td> <textarea name="title" cols="45" rows="3" wrap="VIRTUAL"><?php echo htmlspecialchars( $type->Fields("title")) ?></textarea> 
      </td>
    </tr>
    <tr> 
      <td valign="top"> <span align="left" class="name">Subtitle</span></td>
      <td> <textarea name="subtitle" cols="45" rows="3" wrap="VIRTUAL"><?php echo htmlspecialchars( $type->Fields("subtitile")) ?></textarea></td>
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
      <td valign="top"> <span align="left" class="name">Source URL</span></td>
      <td><input name="sourceurl" size="50" value="<?php echo $type->Fields("sourceurl")?>" > 
      </td>
    </tr>
    <tr> 
      <td valign="top"> <span align="left" class="name">Contact</span></td>
      <td> <textarea name="contact" cols="45" rows="2" wrap="VIRTUAL"><?php echo $type->Fields("contact")?></textarea> 
      </td>
    </tr>
    <tr> 
      <td valign="top"><span align="left" class="name">Date</span><span class="red">*</span><br> 
      </td>
      <td valign="top" class="text"> <input type="text" name="date" size="25" value="<?php echo DateConvertOut($type->Fields("date"))?>">
        (12-30-2002)<br> <input <?php If (($type->Fields("usedate")) == "1") { echo "CHECKED";} ?> type="checkbox" name="usedate" value="1">
        DO NOT DISPLAY DATE</td>
    </tr>
    <tr class="intitle"> 
      <td colspan="2" valign="top"><?php echo helpme("type"); ?>
              Content Type and Navigation</td>
    </tr>
    <td valign="top"> <span align="left" class="name">Nav Text</span><span class="red">*</span></td>
    <td> <input name="linktext" size="50" value="<?php echo $type->Fields("linktext")?>" > 
    </td>
    </tr>
    <tr> 
            <td valign="top"> <span align="left" class="name">Section</span><span class="red"> 
              NEW*</span></td>
      <td class="text"> <select name="type">
	  <OPTION VALUE="<?php echo  $typelab->Fields("id")?>" SELECTED><?php echo  $typelab->Fields("type")?></option>
	  
	  
	  <?php echo $obj->select_type_tree(0); ?></Select>
        <A href="type_add.php" target="_blank">add new</a></td>
    </tr>
	<?php
   $typelab=$dbcon->Execute("SELECT id, type FROM articletype2 ORDER BY type ASC") or DIE($dbcon->ErrorMsg());
   $typelab_numRows=0;
   $typelab__totalRows=$typelab->RecordCount();
?><?php
   $sublab=$dbcon->Execute("SELECT id, subname FROM articlesubtype") or DIE($dbcon->ErrorMsg());
   $sublab_numRows=0;
   $sublab__totalRows=$sublab->RecordCount();
?><?php
   $catagory=$dbcon->Execute("SELECT * FROM catagory ORDER BY catname ASC") or DIE($dbcon->ErrorMsg());
   $catagory_numRows=0;
   $catagory__totalRows=$catagory->RecordCount();
?>
	<tr bgcolor="#FFFFCC"> 
            <td valign="top"> <span align="left" class="name">Section</span><span class="red">*</span></td>
            <td class="text"> <select name="typexxx" id="typexxx">
                <option value="1">none</option>
                <?php
  if ($typelab__totalRows > 0){
    $typelab__index=0;
    $typelab->MoveFirst();
    WHILE ($typelab__index < $typelab__totalRows){
?>
                <option value="<?php echo  $typelab->Fields("id")?>"<?php if ($typelab->Fields("id")==$type->Fields("type2")) echo "SELECTED";?>> 
                <?php echo  $typelab->Fields("type");?> </option>
                <?php
      $typelab->MoveNext();
      $typelab__index++;
    }
    $typelab__index=0;  
    $typelab->MoveFirst();
  }
?>
              </select> <A href="type_add.php" target="_blank">add new</a>
              <input type="hidden" name="oldtype" value="<?php echo $type->Fields("type") ?>"></td>
          </tr>
          <tr bgcolor="#FFFFCC"> 
            <td valign="top"> <span align="left" class="name">Subsection</span><span class="red">*</span></td>
            <td class="text"> <select name="subtypexxx" id="subtypexxx">
                <option value="1">none</option>
                <?php
  if ($sublab__totalRows > 0){
    $sublab__index=0;
    $sublab->MoveFirst();
    WHILE ($sublab__index < $sublab__totalRows){
?>
                <OPTION VALUE="<?php echo  $sublab->Fields("id")?>"<?php if ($sublab->Fields("id")==$type->Fields("subtype")) echo "SELECTED";?>> 
                <?php echo  $sublab->Fields("subname");?> </OPTION>
                <?php
      $sublab->MoveNext();
      $sublab__index++;
    }
    $sublab__index=0;  
    $sublab->MoveFirst();
  }
?>
              </select><?php echo $type->Fields("subtype") ?> <A href="subtype_add.php" target="_blank">add new</a></td>
          </tr>
          <tr bgcolor="#FFFFCC"> 
            <td valign="top"><span align="left" class="name">3rd Level</span><span class="red">*</span></td>
            <td valign="top" class="text"> <select name="select3xxx" id="select3xxx">
                <option value="1">none</option>
                <?php
  if ($catagory__totalRows > 0){
    $catagory__index=0;
    $catagory->MoveFirst();
    WHILE ($catagory__index < $catagory__totalRows){
?>
                <OPTION VALUE="<?php echo  $catagory->Fields("id")?>"<?php if ($catagory->Fields("id")==$type->Fields("catagory")) echo "SELECTED";?>> 
                <?php echo  $catagory->Fields("catname");?> </OPTION>
                <?php
      $catagory->MoveNext();
      $catagory__index++;
    }
    $catagory__index=0;  
    $catagory->MoveFirst();
  }
?>
              </select> <a href="catagory_add.php" target="_blank">add new</a> 
            </td>
          </tr>
    <tr> 
      <td valign="top"><span align="left" class="name">Class</span><span class="red">*</span></td>
      <td valign="top" class="text"> <select name="class" id="class">
          <?php
  if ($class__totalRows > 0){
    $class__index=0;
    $class->MoveFirst();
    WHILE ($class__index < $class__totalRows){
?>
          <OPTION VALUE="<?php echo  $class->Fields("id")?>" <?php if ($class->Fields("id")==$type->Fields("class")) echo "SELECTED";?>> 
          <?php echo  $class->Fields("class");?> </OPTION>
          <?php
      $class->MoveNext();
      $class__index++;
    }
    $class__index=0;  
    $class->MoveFirst();
  }
?>
        </select> </td>
    </tr>
    <td valign="top"><span align="left" class="name">Offsite URL</span></td>
    <td> <input type="text" name="link" size="50" value="<?php echo $type->Fields("link")?>"> 
      <br> <input <?php If (($type->Fields("linkover")) == "1") { echo "CHECKED";} ?> type="checkbox" name="linkuse"> 
      <span class="text">USE THIS URL AS NAV LINK</span> </td>
    </tr>
    <tr class="intitle"> 
      <td colspan="2" valign="top"><?php echo helpme("display"); ?>
              Display and Publishing</td>
    </tr>
    <tr> 
      <td colspan="2" valign="top"><input <?php If (($type->Fields("publish")) == "1") { echo "CHECKED";} If (($type->Fields("id")) == $NULL) { echo "CHECKED";}?>  type="checkbox" name="publish" value="1"> 
        <font color="#990000" size="3"><strong>PUBLISH </strong></font></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="text"><input <?php If (($type->Fields("uselink")) == "1") { echo "CHECKED";} 
					If (($type->Fields("id")) == $NULL) { echo "CHECKED";} ?> type="checkbox" name="uselink" value="1">
        SHOW LINK IN NAVIGATION</td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="text"><input name="fplink" type="checkbox" id="fplink" value="1" <?php If (($type->Fields("fplink")) == "1") { echo "CHECKED";}  ?>>
        SHOW ON FRONT PAGE (NEWS ONLY)</td>
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
      <td colspan="2" valign="top"><?php echo helpme("content"); ?>
              Content Body</td>
    </tr>
    <td colspan="2" valign="top" class="name"> <p align="left">Short Description 
        * <br>
        <textarea name="textfield" cols="65" rows="5" wrap="VIRTUAL"><?php echo htmlspecialchars( $type->Fields("shortdesc"))?></textarea>
    </td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="name">Full Text (not applicable for 
        offsite URLs)<br> 
		
		
		
		
		<?php 
$browser =  strstr(getenv('HTTP_USER_AGENT'), 'MSIE') ;
$browser2 =  strstr(getenv('HTTP_USER_AGENT'), 'Win') ;
 
if (($browser) && ($browser2)) { 
   if (($type->Fields("html")) != "1") 
   {$textvalue = nl2br($type->Fields("test"));}
   else 
   {$textvalue = $type->Fields("test");}
 $demo_array = $spaw_dropdown_data;
// unset current styles
unset($demo_array['style']);
// set new styles
$demo_array['style']['text'] = 'text';
$demo_array['style']['title'] = 'title';
$demo_array['style']['subtitle'] = 'subtitle';
$demo_array['style']['bodystrong'] = 'bodystrong';
$demo_array['style']['hometitle'] = 'hometitle';
$demo_array['style']['homebody'] = 'homebody';
$demo_array['style']['eventname'] = 'eventname';

$sw = new SPAW_Wysiwyg('article' /*name*/,$textvalue /*value*/,                       'en' /*language*/, 'default' /*toolbar mode*/, '' /*theme*/,'500px' /*width*/, '400px' /*height*/, '../styles.css' /*stylesheet file*/,$demo_array /*dropdown data*/);



$sw->show();?>
<input name="html" type="hidden" value="1"><?php
} else {?> <input name="html" type="checkbox" value="1"  <?php If (($type->Fields("html")) == "1") { echo "CHECKED";} ?>>
              HTML Override (<font color="#FF0000">Mac Users uncheck to convert 
              line breaks to &lt;br&gt;</font>)<br> 
             <textarea name="article" cols="65" rows="20" wrap="VIRTUAL"><?php
		$text2 = $type->Fields("test");
		if (($type->Fields("html")) == "1"){
		$text2 = str_replace("<BR>", "<BR>\r\n", $text2);} 
		 echo $text2; ?></textarea>
		<?php } ?>
		
        <br>
        Note: When using Internet Explorer you can not input articles longer than 
        30,000 characters</td>
    </tr>
    <tr class="intitle"> 
      <td colspan="2" valign="top"><?php echo helpme("image"); ?>
              Image (to appear on front page and in first paragraph of article)</td>
    </tr>
    <tr> 
      <td valign="top" class="name">Image Filename</td>
      <td> <input type="text" name="picture" size="50" value="<?php echo $type->Fields("picture")?>"> 
      </td>
    </tr>
    <tr class="text"> 
      <td valign="top"><div align="right"></div></td>
      <td><p> &nbsp;<a href="imgdir.php" target="_blank">view images</a> | <a href="imgup.php" target="_blank">upload 
          image</a><br>
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
      <td colspan="2" valign="top"><?php echo helpme("docs"); ?>
              Attached Document</td>
    </tr>
    <tr> 
      <td valign="top"><span align="left" class="name">Document Name</span></td>
      <td> <input name="doc" size="50" value="<?php echo $type->Fields("doc")?>" > 
        <br> <span class="text"><a href="docdir.php" target="_blank">view documents</a> 
        | <a href="doc_upload.php" target="_blank">upload document</a> </span></td>
    </tr>
    <tr> 
      <td valign="top" class="name">Document Type</td>
      <td><span class="text"> 
        <input <?php If ($type->Fields("doctype") == "pdf") echo("CHECKED");?> type="radio" name="radiobutton" value="pdf">
        pdf 
        <input <?php If ($type->Fields("doctype") == "word") echo("CHECKED");?> type="radio" name="radiobutton" value="word">
        word 
        <input <?php If ($type->Fields("doctype") == "img") echo("CHECKED");?> type="radio" name="radiobutton" value="img">
        image </span></td>
    </tr>
    <tr class="intitle"> 
      <td colspan="2" valign="top"><?php echo helpme("action"); ?>
              Take Action </td>
    </tr>
    <tr> 
      <td valign="top"> <span align="left" class="name">Action Item</span></td>
      <td class="text"> <select name="actionlink">
          <?php
  if ($action__totalRows > 0){
    $action__index=0;
    $action->MoveFirst();
    WHILE ($action__index < $action__totalRows){
?>
          <OPTION VALUE="<?php echo  $action->Fields("id")?>"> <?php echo  $action->Fields("subject");?> 
          </OPTION>
          <?php
      $action->MoveNext();
      $action__index++;
    }
    $action__index=0;  
    $action->MoveFirst();
  }
?>
        </select> <A href="sendfax_add.php" target="_blank" class="text">add new 
        action</a><br> <input <?php If (($type->Fields("actionitem")) == "1") { echo "CHECKED";} ?> type="checkbox" name="actionitem2">
        USE ACTION </td>
    </tr>
    <tr class="intitle"> 
      <td colspan="2" valign="top"><?php echo helpme("region"); ?>
              Regional Content</td>
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
    <tr class="intitle"> 
      <td colspan="2" valign="top"><?php echo helpme("front"); ?>
              Front Page Info (this is for front page content only)</td>
    </tr>
    <tr> 
      <td valign="top"><span align="left" class="name">&quot;More&quot; Link</span></td>
      <td> <input name="morelink" type="text" id="morelink" size="50" value="<?php echo $type->Fields("morelink")?>" > 
      </td>
    </tr>
    <tr> 
      <td valign="top">&nbsp;</td>
      <td> <input name="usemore" type="checkbox" id="usemore" <?php If (($type->Fields("usemore")) == "1") { echo "CHECKED";} ?>> 
        <span align="left" class="name">USE LINK</span></td>
    </tr>
    <tr> 
      <td valign="top"><span align="left" class="name">Front Page Order</span></td>
      <td><input name="pageorder" type="text" id="pageorder" size="5" value="<?php echo $type->Fields("pageorder")?>" ></td>
    </tr>
	  
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("notes"); ?>
              Editor Notes</td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><textarea name="notes" cols="65" rows="5" wrap="VIRTUAL"><?php echo htmlspecialchars( $type->Fields("notes"))?></textarea></td>
          </tr>
    <tr class="intitle"> 
      <td colspan="2" valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2" valign="top"><input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"> 
        <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> 
        <input type="submit" name="preview" value="Preview" onclick="return confirmSubmit('Please save this record first or all changes will be lost\nPress OK to continue or CANCEL to return and save you work')"></td>
    </tr>
  </table>
              
	<input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
     
	 
	  </form>

<?php
  $type->Close();
  $typelab->Close();
  $sublab->Close();
  $catagory->Close();
   $class->Close();
  $action->Close();
  $state->Close();?>
<?php include ("footer.php"); ?>