<?php
require("Connections/freedomrising.php");
include("Connections/menu.class.php");
include("FCKeditor/fckeditor.php"); 
include("../includes/versionfunctions.php");
$buildform = new BuildForm;
$obj = new Menu; 
  
  
if ($userper[2] or  $userper[1] ) { } else { header ("Location: index.php"); }
if (isset($preview)) {header ("Location: ../article.php?id=$id&preview=1");} 


ob_start();

if ($_GET[restore]) {
	articleversionrestore($_GET[restore]);
	redirect("articlelist.php");
}

if ((($_POST[MM_update]) && ($_POST[MM_recordId])) or ($_POST[MM_insert]) or (($_POST[MM_delete]) && ($_POST[MM_recordId]))) {
   //set non POST passed varablies
	if (isset($MM_insert)) {
		$_POST[datecreated] = date("y-n-j");;
		$_POST[enteredby] = $ID;
	}
	// add version control
	else if ( (isset($MM_update)) or (isset($_POST[MM_delete])) ) {
		articleversion($_POST[MM_recordId]);
	}
	
	$date =  DateConvertIn($date);
	$_POST[textfield] =htmlspecialchars($textfield);
	if ($_POST[mlink]) { 
		$link = $_POST[mlink];
		$linkuse = 1;
	 }
	$MM_editColumn = "id";  
    $MM_editTable  = "articles";
    $MM_recordId = $_POST[MM_recordId];
    $MM_editRedirectUrl = "article_list.php?type=$type";
	$MM_fieldsStr = "relsection1|value|relsection2|value|type|value|subtype|value|select3|value|uselink|value|publish|value|title|value|subtitle|value|html|value|article|value|textfield|value|author|value|linktext|value|date|value|usedate|value|doc|value|radiobutton|value|link|value|linkuse|value|new|value|actionitem|value|actionlink|value|piccap|value|picture|value|usepict|value|morelink|value|usemore|value|pageorder|value|class|value|source|value|contact|value|alignment|value|alttag|value|state|value|pselection|value|fplink|value|ID|value|enteredby|value|datecreated|value|sourceurl|value|notes|value|comments|value|navtext|value|custom1|value|custom2|value|custom3|value|custom4|value ";
    $MM_columnsStr = "relsection1|none,none,1|relsection2|none,none,1|type|none,none,NULL|subtype|none,none,NULL|catagory|none,none,NULL|uselink|none,1,0|publish|none,none,0|title|',none,''|subtitile|',none,''|html|none,1,0|test|',none,''|shortdesc|',none,''|author|',none,''|linktext|',none,''|date|',none,NULL|usedate|none,1,0|doc|',none,''|doctype|',none,''|link|',none,''|linkover|none,1,0|new|none,1,0|actionitem|none,1,0|actionlink|',none,''|piccap|',none,''|picture|',none,''|picuse|none,none,NULL|morelink|',none,''|usemore|none,1,0|pageorder|none,none,NULL|class|none,none,NULL|source|',none,''|contact|',none,''|alignment|',none,''|alttag|',none,''|state|none,none,NULL|pselection|',none,''|fplink|',none,''|updatedby|',none,''|enteredby|',none,''|datecreated|',none,''|sourceurl|',none,''|notes|',none,''|comments|none,1,0|navtext|',none,''|custom1|',none,''|custom2|',none,''|custom3|',none,''|custom4|',none,''";
	//databaseactions();
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");


#### multi sectional ####### 
	if ($MM_reltype) {
		if ($MM_insert) {
			$MM_recordId = $dbcon->Insert_ID();
 		} 
		$reldelete=$dbcon->Execute("Delete FROM articlereltype WHERE articleid =$MM_recordId") or DIE($dbcon->ErrorMsg());
			if (!$MM_delete) {
    			while (list($k, $v) = each($reltype)) 
    		{ 
			$relupdate=$dbcon->Execute("INSERT INTO articlereltype VALUES ( $MM_recordId,$v)") or DIE($dbcon->ErrorMsg());
			}
		}	
	}

ob_end_flush();
}

//BUILD QUERIES  

#CONTENT
$r__MMColParam = "90000000000";

if (isset($_GET["id"])) {
	$r__MMColParam = $_GET["id"];
	$id = $_GET["id"];
	}
$r=$dbcon->Execute("SELECT * FROM articles WHERE id = " . ($r__MMColParam) . "") or DIE("71".$dbcon->ErrorMsg());

//pull from version table if called
if (isset($_GET[vid])) {
	$r=$dbcon->Execute("SELECT * FROM articles_version WHERE vid = " . $_GET[vid] . "") or DIE("75".$dbcon->ErrorMsg());	
	$id = $r->Fields("id");
}

if (isset($id)) {
	$rvar=$r->Fields("type");
	}
else {$rvar=1;}

// check for sectional permission settings 
if ($_GET[id]) {
	if ($userper[97]){
		if ($sectional_per[$rvar] ) {}
		else { redirect("index.php"); }
	}
}

$rlab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$rvar."") or DIE("92".$dbcon->ErrorMsg());

if (isset($id)) {	$secvar1=$r->Fields("relsection1");}
else {$secvar1=1;}
 
$rel1q=$dbcon->Execute("SELECT id, type FROM articletype where id =$secvar1") or DIE("97".$dbcon->ErrorMsg());
if (isset($id)) {$secvar2=$r->Fields("relsection2");}
else {$secvar2=1;}
$rel2q=$dbcon->Execute("SELECT id, type FROM articletype where id =$secvar2") or DIE($dbcon->ErrorMsg());
$id = $r__MMColParam;
if ($MM_reltype) {
	$related=$dbcon->Execute("SELECT t.type, a.typeid FROM articlereltype a, articletype t where  t.id =a.typeid and articleid = $id") or DIE("102".$dbcon->ErrorMsg());
	}

//other queries
$class=$dbcon->Execute("SELECT id, class FROM class ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
$class_numRows=0;
$class__totalRows=$class->RecordCount();
$state=$dbcon->Execute("SELECT * FROM region order by title asc") or DIE($dbcon->ErrorMsg());
$state_numRows=0;
$state__totalRows=$state->RecordCount();
$modsel=$dbcon->Execute("SELECT a.link, a.title FROM articles a, articletype t where a.type = t.id and  t.type = 'Module Pages' ORDER BY a.title ASC") or DIE($dbcon->ErrorMsg());
	

?><?php include ("header.php"); ?>

<form ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" METHOD="POST">
             
              
        
        <table width="100%" border="0" align="center">
          <tr class="banner"> 
            <td colspan="2" valign="top"><?php echo helpme("Overview"); ?>Add/Edit 
              Content</td>
          </tr></table>
		  
		  	  <script type="text/javascript">


function change(which) {
    document.getElementById('main').style.display = 'none';
document.getElementById('picture').style.display = 'none'; 
document.getElementById('advanced').style.display = 'none'; 
    document.getElementById(which).style.display = 'block';
	
    }


</script>

 
<table width="100%" border="0" align="center">
          <tr> 
            <td colspan="2" valign="top" class="name">ID #<?php echo $r->Fields("id")?> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" valign="top" class="name"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="name">
                <tr> 
                  <td width="25%">Date Created</td>
                  <td width="25%"><div align="left"><?php echo DoDateTime($r->Fields("datecreated"),("n/j/y"))?></div></td>
                  <td width="25%">Date Modified</td>
                  <td width="25%"> <div align="left"> 
                      <?php if ($r->Fields("updated")!= NULL) { echo DoTimeStamp($r->Fields("updated"),("n/j/y"));}?>
                    </div></td>
                </tr>
                <tr> 
                  <td>Created By</td>
                  <td><div align="left"> 
                      <?php if ($r->Fields("enteredby")!= NULL) {
	$users=$dbcon->Execute("SELECT name FROM users where id =".$r->Fields("enteredby")."") or DIE($dbcon->ErrorMsg());
	echo $users->Fields("name");}?>
                    </div></td>
                  <td>Last Modified BY</td>
                  <td><div align="left"> 
                      <?php if ($r->Fields("updatedby")!= NULL) {
	$users=$dbcon->Execute("SELECT name FROM users where id =".$r->Fields("updatedby")."") or DIE($dbcon->ErrorMsg());
	echo $users->Fields("name");}?>
                    </div></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"> 
           <?php  if ($userper[98]){ ?>   <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> <?php } ?>
              <input type="submit" name="preview" value="Preview" onclick="return confirmSubmit('Please save this record first or all changes will be lost\nPress OK to continue or CANCEL to return and save you work')"></td>
          </tr></table>
	<br><ul id="topnav">
	<li class="tab1"><a href="#" id="a0" onclick="change('main');" >Main Content</a></li>
	<li class="tab2"><a href="#" id="a1" onclick="change('picture');" >Images and Documents</a></li>
	<li class="tab3"><a href="#" id="a2" onclick="change('advanced');" >Advanced Options </a></li>
</ul>
		 
		  <div id="main" class="main" >
		  <table width="100%" border="0" align="center">       <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Header"); ?>Title</td>
          </tr>
		  <?php  if ($userper[98]){ ?>
		   <tr> 
            <td colspan="2" valign="top"><input <?php If (($r->Fields("publish")) == "1") { echo "CHECKED";} If (($r->Fields("id")) == $NULL) { echo "CHECKED";}?>  type="checkbox" name="publish" value="1"> 
              <font color="#990000" size="3"><strong>PUBLISH </strong></font></td>
          </tr><?php }
		  else { ?><input name="publish" type="hidden" value="<?php echo $r->Fields("publish");?>"> <?php }  ?>
		  
          <tr> 
            <td width="24%" valign="top"> <span align="left" class="name">Title </span
			><span class="red">*</span></td>
            <td width="76%"> <textarea name="title" cols="45" rows="3" wrap="VIRTUAL"><?php echo htmlspecialchars( $r->Fields("title")) ?></textarea> 
            </td>
          </tr>
          <tr> 
            <td valign="top"> <span align="left" class="name">Subtitle</span></td>
            <td> <textarea name="subtitle" cols="45" rows="3" wrap="VIRTUAL"><?php echo htmlspecialchars( $r->Fields("subtitile")) ?></textarea></td>
          </tr>
          
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Content Type and Navigation"); ?> 
              Section and Class </td>
          </tr>
		  
          <tr> 
            <td valign="top"> <span align="left" class="name"> <?php if (isset($MM_reltype)) {?>Main <?php }?>Section</span><span class="red">*</span></td>
            <td class="text"> <select name="type">
                <option value="<?php echo  $rlab->Fields("id")?>" selected><?php echo  $rlab->Fields("type")?></option>
                <?php echo $obj->select_type_tree($MX_top); ?>
              </select><?php if ($userper[4]) { ?>
              <A href="type_edit.php" target="_blank">add new</a><?php }?></td>
			  <?php if (isset($MM_reltype)) {?>
          </tr>
		   <tr> 
            <td valign="top"> <span align="left" class="name">Related Sections</span></td>
            <td class="text"> <select multiple name='reltype[]' size='8'>
			<?php while ((!$related->EOF)){ ?>
                <option value="<?php echo  $related->Fields("typeid")?>" selected ><?php echo  $related->Fields("type")?></option>
			<?php 	$related->MoveNext(); }?>
                <?php echo $obj->select_type_tree($MX_top); ?>
              </select>
              </td>
          </tr>
          <?php } if (isset($relsection1id)) {?>
          <tr> 
            <td valign="top"> <span align="left" class="name"><?php echo $relsection1label ;?></span></td>
            <td class="text"> <select name="relsection1">
                <OPTION VALUE="<?php echo  $r->Fields("relsection1")?>" SELECTED><?php echo  $rel1q->Fields("type")?></option>
                <?php echo $obj->select_type_tree($relsection1id); ?> </Select> 
            </td>
          </tr>
          <?php } 
	 if (isset($relsection2id)) {
	
	?>
          <tr> 
            <td valign="top"> <span align="left" class="name"><?php echo $relsection2label ;?></span></td>
            <td class="text"> <select name="relsection2">
                <option value="<?php echo  $r->Fields("relsection2")?>" selected><?php echo  $rel2q->Fields("type")?></option>
                <?php echo $obj->select_type_tree($relsection2id); ?> </select></td>
          </tr>
          <?php }   	?>
          <tr> 
            <td valign="top"><span align="left" class="name">Class</span><span class="red">*</span></td>
            <td valign="top" class="text"> <select name="class" id="class">
                <?php
  if ($class__totalRows > 0){
    $class__index=0;
    $class->MoveFirst();
    WHILE ($class__index < $class__totalRows){
?>
                <OPTION VALUE="<?php echo  $class->Fields("id")?>" <?php if ($class->Fields("id")==$r->Fields("class")) echo "SELECTED";?>> 
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
      
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Content Body"); ?> 
              Content Body</td>
          </tr>
          <td colspan="2" valign="top" class="name"> <p align="left">Short Description<br>
              <textarea name="textfield" cols="65" rows="5" wrap="VIRTUAL"><?php echo htmlspecialchars( $r->Fields("shortdesc"))?></textarea>
          </td>
          </tr>
          <tr> 
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

</script> <textarea id = "articlemo" name="article" cols="80" rows="60" wrap="VIRTUAL" style="width:100%"><?php   if (($r->Fields("html")) != "1") 
   {$textvalue = nl2br($r->Fields("test"));}
   else 
   {$textvalue = $r->Fields("test");} 
   echo $textvalue;
   ?></textarea> <input name="html" type="hidden" value="1"> 
              <?php }
 
elseif (($browser_ie) && ($browser_win)) { 
   if (($r->Fields("html")) != "1") 
   {$textvalue = nl2br($r->Fields("test"));}
   else 
   {$textvalue = $r->Fields("test");}

$oFCKeditor = new FCKeditor ;
$oFCKeditor->Value = $textvalue  ;
$oFCKeditor->CreateFCKeditor( 'article', '500', 500 ) ;

?>
              <input name="html" type="hidden" value="1">
              <?php
} else {?>
              <input name="html" type="checkbox" value="1"  <?php If (($r->Fields("html")) == "1") { echo "CHECKED";} ?>>
              HTML Override (<font color="#FF0000">If you do not see a WYSIWYG 
              editor below please use a Mozilla browser<br> <textarea name="article" cols="65" rows="20" wrap="VIRTUAL"><?php
		$text2 = $r->Fields("test");
		if (($r->Fields("html")) == "1"){
		$text2 = str_replace("<BR>", "<BR>\r\n", $text2);} 
		 echo $text2; ?></textarea> 
              <?php } ?>
              <br> 
              <?php if  ($browser_ie) { echo "Note: When using Internet Explorer you can not input articles longer than 
        30,000 characters";}?>
            </td>
          </tr>
		   <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Display and Publishing"); ?> 
              Content Information </td>
          </tr>
		  <tr> 
            <td valign="top"> <span align="left" class="name">Author</span></td>
            <td> <input name="author" size="50" value="<?php echo htmlspecialchars($r->Fields("author"))?>" > 
            </td>
          </tr>
          <tr> 
            <td valign="top"> <span align="left" class="name">Source</span></td>
            <td><input name="source" size="50" value="<?php echo htmlspecialchars($r->Fields("source"))?>" > 
            </td>
          </tr>
          <tr> 
            <td valign="top"> <span align="left" class="name">Source URL</span></td>
            <td><input name="sourceurl" size="50" value="<?php echo $r->Fields("sourceurl")?>" > 
            </td>
          </tr>
       
          <tr> 
            <td valign="top"><span align="left" class="name">Date</span><br> </td>
            <td valign="top" class="text"> <input type="text" name="date" size="25" value="<?php echo DateConvertOut($r->Fields("date"))?>">
			
			<script language="javascript"><!-- 
if (!document.layers) {
document.write("&nbsp;<img src='images/cal.gif' onclick='popUpCalendar(this, date, \"mm-dd-yyyy\")' alt='show calendar'>")
}
//-->
</script>
              (12-30-2002)<br> 
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DO NOT DISPLAY DATE
              <input <?php If (($r->Fields("usedate")) == "1") { echo "CHECKED";} ?> type="checkbox" name="usedate" value="1"></td>
          </tr>
		   <tr> 
            <td valign="top"> <span align="left" class="name">Order (sorts by date if blank)</span></td>
            <td><input name="pageorder" size="10" value="<?php echo $r->Fields("pageorder")?>" > 
            </td>
          </tr>
		     <tr> 
            <td valign="top"> <span align="left" class="name">Press Release Contact</span></td>
            <td> <textarea name="contact" cols="45" rows="2" wrap="VIRTUAL"><?php echo htmlspecialchars($r->Fields("contact"))?></textarea> 
            </td>
          </tr>
		  </table>
		  </div>
		    <div id="picture" style="display: none ;">
		  <table width="100%" border="0" align="center"> 
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Image"); ?> Image 
              (to appear on front page and in first paragraph of article)</td>
          </tr>
          <tr> 
            <td valign="top" class="name">Image Filename</td>
            <td> <input type="text" name="picture" size="50" value="<?php echo $r->Fields("picture")?>"> 
            </td>
          </tr>
          <tr class="text"> 
            <td valign="top"><div align="right"></div></td>
            <td><p> &nbsp;<a href="imgdir.php" target="_blank">view images</a> 
                | <a href="imgup.php" target="_blank">upload image</a><br>
                <input <?php If (($r->Fields("picuse")) == "1") { echo "CHECKED";} ?> type="checkbox" name="usepict" value="1">
                USE THIS IMAGE<br>
              </p></td>
          </tr>
          <tr> 
            <td valign="top" class="name">Image Selection</td>
            <td class="text"> <input type="radio" name="pselection" value="original" <?php if ($r->Fields("pselection") == "original") echo("CHECKED");?>>
              Original 
              <input name="pselection" type="radio" value="pic" <?php if ($r->Fields("pselection") == "pic") echo("CHECKED");?>>
              Optimized </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Alignment</td>
            <td class="text"> <input type="radio" name="alignment" value="left" <?php if ($r->Fields("alignment") == "left") echo("CHECKED");?>>
              Left 
              <input name="alignment" type="radio" value="right" <?php if ($r->Fields("alignment") == "right") echo("CHECKED");?>>
              Right</td>
          </tr>
          <tr> 
            <td valign="top" class="name">Image Caption</td>
            <td> <input type="textarea" name="piccap" size="50" value="<?php echo $r->Fields("piccap")?>"> 
            </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Alt Tag<br>
              (short!)</td>
            <td> <input name="alttag" type="textarea" id="alttag" value="<?php echo $r->Fields("alttag")?>" size="50"> 
            </td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Attached Document"); ?> 
              Attached Document</td>
          </tr>
          <tr> 
            <td valign="top"><span align="left" class="name">Document Name</span></td>
            <td> <input name="doc" size="50" value="<?php echo $r->Fields("doc")?>" > 
              <br> <span class="text"><a href="docdir.php" target="_blank">view 
              documents</a> | <a href="doc_upload.php" target="_blank">upload 
              document</a> </span></td>
          </tr>
          <tr> 
            <td valign="top" class="name">Document Type</td>
            <td><span class="text"> 
              <input <?php If ($r->Fields("doctype") == "pdf") echo("CHECKED");?> type="radio" name="radiobutton" value="pdf">
              pdf 
              <input <?php If ($r->Fields("doctype") == "word") echo("CHECKED");?> type="radio" name="radiobutton" value="word">
              word 
              <input <?php If ($r->Fields("doctype") == "img") echo("CHECKED");?> type="radio" name="radiobutton" value="img">
              image </span></td>
          </tr>
		   </table>
		  </div>
		    <div id="advanced" style="display: none;">
		  <table width="100%" border="0" align="center"> 
		  <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Display and Publishing"); ?> 
             Advanced Publishing Options </td>
          </tr>
		  <tr>
          <td valign="top"> <span align="left" class="name">Alt Navigation Text</span></td>
          <td> <input name="linktext" size="50" value="<?php echo $r->Fields("linktext")?>" > 
          </td>
          </tr>
		    
		  <tr>
          <td valign="top"><span align="left" class="name">Offsite URL</span></td>
          <td> <input type="text" name="link" size="50" value="<?php echo $r->Fields("link")?>"> 
            <br> <input <?php If (($r->Fields("linkover")) == "1") { echo "CHECKED";} ?> type="checkbox" name="linkuse"> 
            <span class="text">USE THIS URL AS NAV LINK</span> </td>
          </tr>
		  <?php if ($modsel->Fields("link") ) { ?>
		   <tr>
          <td valign="top"><span align="left" class="name">Link to Module</span></td>
          <td> <select name="mlink">
		  <option value="">Select Module</option>
		  <?php while (!$modsel->EOF) { ?>
		  <option value="<?php echo $modsel->Fields("link")?>"><?php echo $modsel->Fields("title")?></option>
		  <?php $modsel->MoveNext();}?>
		  </select>
             </td>
          </tr>
		  <?php }?>
		  <tr> 
            <td colspan="2" valign="top" class="text"><input <?php If (($r->Fields("uselink")) == "1") { echo "CHECKED";} 
					If (($r->Fields("id")) == $NULL) { echo "CHECKED";} ?> type="checkbox" name="uselink" value="1">
              SHOW LINK IN NAVIGATION</td>
          </tr>
		  <tr>
		  <td colspan="2" valign="top" class="text"><input name="fplink" type="checkbox" id="fplink" value="1" <?php If (($r->Fields("fplink")) == "1") { echo "CHECKED";}  ?>>
              <?php  if ($MM_fplink) { echo $MM_fplink; } else { echo  "SHOW FRONT PAGE LINK"; }?></td>
          </tr>
          <?php
		  
		  ###### you have to reinsert the php into the html to makes work again ####
		   // If (($r->Fields("new")) == "1") { echo "CHECKED";} ?>
          <tr> 
            <td colspan="2" valign="top" class="text"><input  type="checkbox" name="new" <?php if (($r->Fields("new")) == "1") { echo "CHECKED";}?>>
              <?php  if ($MM_new) { echo $MM_new; } else { echo "LISTED AS NEW";} ?> </td></tr>
			  <?php 
		if (($AMP_customartfield1) or ($AMP_customartfield2) or ($AMP_customartfield3) or ($AMP_customartfield4)  ) {
			echo $buildform->add_header('Custom AMP Fields');
			if ($AMP_customartfield1) {
				echo addfield($AMP_customartfield1[0],$AMP_customartfield1[1],$AMP_customartfield1[2],$r->Fields("custom1"),$AMP_customartfield1[4]);
			}
			if ($AMP_customartfield2) {
				echo addfield($AMP_customartfield2[0],$AMP_customartfield2[1],$AMP_customartfield2[2],$r->Fields("custom2"),$AMP_customartfield2[4]);
			}
			if ($AMP_customartfield3) {
				echo addfield($AMP_customartfield3[0],$AMP_customartfield3[1],$AMP_customartfield3[2],$r->Fields("custom3"),$AMP_customartfield3[4]);
			}
			if ($AMP_customartfield4) {
				echo addfield($AMP_customartfield4[0],$AMP_customartfield4[1],$AMP_customartfield4[2],$r->Fields("custom4"),$AMP_customartfield4[4]);
			}
		}
			?>
		     <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Editor Notes"); ?> 
              Page Specific Navigation Text </td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><textarea name="navtext" cols="65" rows="5" wrap="VIRTUAL" id="navtext"><?php echo htmlspecialchars( $r->Fields("navtext"))?></textarea></td>
          </tr>
          
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("User Comments"); ?> 
              User Comments</td>
          </tr>
		  <tr> 
            <td valign="top"><span align="left" class="name"> Allow User Comments</span></td>
            <td class="text"> 
              <input name="comments" type="checkbox" id="comments" value="1" <?php If (($r->Fields("comments")) == "1") { echo "CHECKED";} ?>>
              <br>
              <a href="comments.php?cid=<?php echo $_GET[id] ;?>" target="_blank" class="text">view/edit 
              user comments</a></td>
          </tr>
		
          <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Regional Content"); ?> 
              Regional Content</td>
          </tr>
          <tr> 
            <td class="name"> Region</td>
            <td><select name="state">
                <option value="">Select Region</option>
                <?php    if ($state__totalRows > 0){
    $state__index=0;
    $state->MoveFirst();
    WHILE ($state__index < $state__totalRows){
?>
                <option value="<?php echo  $state->Fields("id")?>" <?php if ($state->Fields("id")==$r->Fields("state")) echo "SELECTED";?>> 
                <?php echo  $state->Fields("title");?> </option>
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
            <td colspan="2" valign="top"><?php echo helpme("Editor Notes"); ?> 
              Editor Notes</td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><textarea name="notes" cols="65" rows="5" wrap="VIRTUAL"><?php echo htmlspecialchars( $r->Fields("notes"))?></textarea></td>
          </tr>
		  </table></div>
		  <table width = "100%">
          <tr class="intitle"> 
            <td colspan="2" valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes">
            <?php  if ($userper[98]){ ?>  <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> <?php }?>
              <input type="submit" name="preview" value="Preview" onclick="return confirmSubmit('Please save this record first or all changes will be lost\nPress OK to continue or CANCEL to return and save you work')"></td>
          </tr>
        </table>
              
	<input type="hidden" name="MM_recordId" value="<?php echo $id; ?>">
     
	 
	  </form>
<?php if ($_GET[id] or $_GET[vid]) { articleversionlist($id);}?>

<?php include ("footer.php"); ?>