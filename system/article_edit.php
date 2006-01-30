<?php
$mod_name='content';
require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
require_once("AMP/Form/HTMLEditor.inc.php");
require_once("../includes/versionfunctions.php");

$buildform = new BuildForm;
$obj = new SysMenu; 
 /* 
  foreach ($_POST as $ps_key=>$ps_value) {
    print $ps_key.": ".$ps_value."<BR>";
  }
 */ 
if (!AMP_Authorized( AMP_PERMISSION_CONTENT_EDIT )) ampredirect ("index.php"); 
if (isset($_GET['preview'])) ampredirect( AMP_URL_AddVars( AMP_CONTENT_URL_ARTICLE, array("id=".$_GET['id'],"preview=1"))); 


ob_start();

if ($_GET['restore']) {
	articleversionrestore($_GET['restore']);
    $org_id = ($_GET['id']);
    ampredirect("article_edit.php?id=$org_id");
}
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

   //set non POST passed varablies

    $userlookup = AMPSystem_Lookup::instance( 'users' );
    $article = trim( $_POST['article'] );
    $_POST['updatedby'] = array_search($_SERVER['REMOTE_USER'], $userlookup);

	if (isset($_POST['MM_insert'])) {
		$_POST['datecreated'] = date("y-n-j");
        $_POST['enteredby'] = $_POST['updatedby'];
		
	}
	// add version control
	else if ( (isset($_POST['MM_update'])) or (isset($_POST['MM_delete'])) ) {
		articleversion($_POST['MM_recordId']);
	}


	//upload picture
	$getimgset=$dbcon->Execute("SELECT thumb, optw, optl FROM sysvar where id =1") or DIE($dbcon->ErrorMsg());
	if ($_FILES['file']['name']) {
		$picture = upload_image('',$getimgset->Fields("optw"),$getimgset->Fields("optl"),$getimgset->Fields("thumb"));
	}
	
	$date =  DateConvertIn($date);
	#$_POST['textfield'] =htmlspecialchars($_POST['textfield']);
	if ($_POST['mlink']) { 
		$link = $_POST['mlink'];
		$linkuse = 1;
	 }
	$MM_editColumn = "id";  
    $MM_editTable  = "articles";
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = "article_list.php?type=".$_POST['type'];

	$MM_fieldsStr = "relsection1|value|relsection2|value|type|value|subtype|value|select3|value|uselink|value|publish|value|title|value|subtitle|value|html|value|article|value|textfield|value|author|value|linktext|value|date|value|usedate|value|doc|value|radiobutton|value|link|value|linkuse|value|new|value|actionitem|value|actionlink|value|piccap|value|picture|value|usepict|value|morelink|value|usemore|value|pageorder|value|class|value|source|value|contact|value|alignment|value|alttag|value|state|value|pselection|value|fplink|value|updatedby|value|enteredby|value|datecreated|value|sourceurl|value|notes|value|comments|value|navtext|value|custom1|value|custom2|value|custom3|value|custom4|value ";
    $MM_columnsStr = "relsection1|none,none,1|relsection2|none,none,1|type|none,none,NULL|subtype|none,none,NULL|catagory|none,none,NULL|uselink|none,1,0|publish|none,none,0|title|',none,''|subtitile|',none,''|html|none,1,0|test|',none,''|shortdesc|',none,''|author|',none,''|linktext|',none,''|date|',none,NULL|usedate|none,1,0|doc|',none,''|doctype|',none,''|link|',none,''|linkover|none,1,0|new|none,1,0|actionitem|none,1,0|actionlink|',none,''|piccap|',none,''|picture|',none,''|picuse|none,none,NULL|morelink|',none,''|usemore|none,1,0|pageorder|none,none,NULL|class|none,none,NULL|source|',none,''|contact|',none,''|alignment|',none,''|alttag|',none,''|state|none,none,NULL|pselection|',none,''|fplink|',none,''|updatedby|',none,''|enteredby|',none,''|datecreated|',none,''|sourceurl|',none,''|notes|',none,''|comments|none,1,0|navtext|',none,''|custom1|',none,''|custom2|',none,''|custom3|',none,''|custom4|',none,''";
	//databaseactions();
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");


#### multi sectional ####### 
	if ($MM_reltype) {
		if ($_POST['MM_insert']) {
			$MM_recordId = $dbcon->Insert_ID();
 		} 
		$reldelete=$dbcon->Execute("Delete FROM articlereltype WHERE articleid =$MM_recordId") or DIE($dbcon->ErrorMsg());
			if (!$MM_delete && is_array($_POST['reltype'])) {
    			while (list($k, $v) = each($_POST['reltype'])) { 
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
$r->MoveFirst();

//pull from version table if called
if (isset($_GET['vid'])) {
	$r=$dbcon->Execute("SELECT * FROM articles_version WHERE vid = " . $_GET['vid'] ) or die("75".$dbcon->ErrorMsg());	
	$id = $r->Fields("id");
}

if (isset($id)) {
	$rvar=$r->Fields("type");
	}
else {$rvar=1;}

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
$coms=$dbcon->Execute("SELECT id, comment FROM comments where articleid = $id  ORDER BY publish desc, date desc") or DIE($dbcon->ErrorMsg());

$class=$dbcon->Execute("SELECT id, class FROM class ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
$class_numRows=0;
$class__totalRows=$class->RecordCount();
$state=$dbcon->Execute("SELECT * FROM region order by title asc") or DIE($dbcon->ErrorMsg());
$state_numRows=0;
$state__totalRows=$state->RecordCount();
$modsel=$dbcon->Execute("SELECT a.link, a.title FROM articles a, articletype t where a.type = t.id and  t.type = 'Module Pages' ORDER BY a.title ASC") or DIE($dbcon->ErrorMsg() );
	

include ("header.php"); ?>
<script language ="Javascript">

// Declaring valid date character, minimum year and maximum year
var dtCh= "-";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : mm/dd/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}

function ValidateForm(){
	var dt=document.form.date;
	if (isDate(dt.value)==false){
		dt.focus();
		alert('This');
		return false;
	}
    return true;
 }
</script>
<script src="/scripts/ajax/prototype.js" type="text/javascript"></script>
<script src="/scripts/ajax/scriptaculous.js" type="text/javascript"></script>
<style>          div.auto_complete {
            width: 350px;
            background: #fff;
          }
          div.auto_complete ul {
            border:1px solid #888;
            margin:0;
            padding:0;
            width:100%;
            list-style-type:none;
          }
          div.auto_complete ul li {
            margin:0;
            padding:3px;
          }
          div.auto_complete ul li.selected { 
            background-color: #ffb; 
          }
          div.auto_complete ul strong.highlight { 
            color: #800; 
            margin:0;
            padding:0;
          }
</style>

<form name="form" ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" METHOD="POST" enctype="multipart/form-data">
             
	<table width="100%" border="0" align="center" bgcolor="#dedede">
		<tr> 
            <td colspan="2" class ="banner" ><?php echo helpme("Overview"); ?>Add/Edit Content</td>
		</tr>
		<tr> 
            <td rowspan="2" class ="doc_info" width="120">Document<br>Info</td>
            <td valign="top" class="name">ID #<?php echo $r->Fields("id")?> </td>
		</tr>
        <tr> 
			<td  valign="top" class="name">
			
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="name">
        			<tr> 
        				<td width="25%">Date Created</td>
                  		<td width="25%"><div align="left"><?php echo DoDateTime($r->Fields("datecreated"),("n/j/y"))?></div></td>
                  		<td width="25%">Date Modified</td>
                  		<td width="25%"> <div align="left"> <?php if ($updated_time = $r->Fields("updated")) { echo $updated_time;}?></div></td>
                  		<!--<td width="25%"> <div align="left"> <?php if ($updated_time = $r->Fields("updated")) { echo DoTimeStamp(strtotime($updated_time),("n/j/y"));}?></div></td>-->
                	</tr>
                	<tr> 
                  		<td>Created By</td>
                  		<td><div align="left"> <?php if ($user_id_creator = $r->Fields("enteredby")) {
								$users=$dbcon->Execute("SELECT name FROM users where id =".$user_id_creator."") or DIE($dbcon->ErrorMsg());
								echo $users->Fields("name");}?></div></td>
                  		<td>Last Modified By</td>
                  		<td><div align="left"> <?php if ($user_id_editor = $r->Fields("updatedby")) {
	$users=$dbcon->Execute("SELECT name FROM users where id =".$user_id_editor."") or DIE($dbcon->ErrorMsg());
	echo $users->Fields("name");}?></div></td>
                	</tr>
              	</table>
			</td>
		</tr>
</table><br>
<input type="submit" name="<?php if (empty($_GET['id'])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes">
<?php  
if (AMP_Authorized( AMP_PERMISSION_CONTENT_USER_ADDED_CONTENT)){ ?>&nbsp;&nbsp;&nbsp;<input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"><?php } ?><br>
<br>

<script type="text/javascript">
function change(which) {
    document.getElementById('main').style.display = 'none';
	document.getElementById('picture').style.display = 'none'; 
	document.getElementById('advanced').style.display = 'none'; 
    document.getElementById(which).style.display = 'block';
    }
function change2(which) {
    document.getElementById('upload').style.display = 'none';
	document.getElementById(which).style.display = 'block';
    }
</script>

<ul id="topnav">
	<li class="tab1"><a href="#" id="a0" onclick="change('main');" >Main Content</a></li>
	<li class="tab2"><a href="#" id="a1" onclick="change('picture');" >Images and Documents</a></li>
	<li class="tab3"><a href="#" id="a2" onclick="change('advanced');" >Advanced Options </a></li>
</ul>
		 
		  <div id="main" class="main" >
		  <table width="100%" border="0" align="center">       <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Header"); ?>Title</td>
          </tr>
		  <?php  if (AMP_Authorized( AMP_PERMISSION_CONTENT_PUBLISH)){ ?>
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
                <?php echo $obj->select_type_tree(1);  ?>
              </select><?php  if (AMP_Authorized( AMP_PERMISSION_CONTENT_SECTION_EDIT)) { ?>
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
                <textarea id="articlexin" name="article" rows="60" cols="80" WRAP="VIRTUAL" style = "width: 100%;"> 
                <?php print $r->Fields("test")?></textarea><BR>
                <?php
                $current_browser = getBrowser();
                if (($_COOKIE['AMPWYSIWYG'] != 'none' ) && ($current_browser == 'win/ie' or $current_browser == 'mozilla' )) {
                    print '<input name="html" type="hidden" value="1"><BR>';
                    $editor = &AMPFormElement_HTMLEditor::instance();
                    $editor->addEditor('articlexin');
                    $editor->height = '800px';
                    print $editor->output();
                } else {
                    print '<input name="html" type="checkbox" value="1"'.
                            ( $r->Fields("html")?" CHECKED":"") . ">HTML Override <br>";
                }

              ?>
            </td>
          </tr>
		   <tr class="intitle"> 
            <td colspan="2" valign="top"><?php echo helpme("Display and Publishing"); ?> 
              Content Information </td>
          </tr>
		  
		  <tr> 
            <td valign="top"> <span align="left" class="name">Author</span></td>
            <td> <input id="author" name="author" size="50" value="<?php echo htmlspecialchars($r->Fields("author"))?>" > 
			<div class="auto_complete" id="author_list"></div>
			<script language="javascript">
  			 new Ajax.Autocompleter("author", "author_list", "ajax_request.php" , {});
			</script> 
            </td>
          </tr>
          <tr> 
            <td valign="top"> <span align="left" class="name">Source</span></td>
            <td><input name="source" id="source" size="50" value="<?php echo htmlspecialchars($r->Fields("source"))?>" > 
				<div class="auto_complete" id="source_list"></div>
			<script language="javascript">
  			 new Ajax.Autocompleter("source", "source_list", "ajax_request.php" , {});
			</script> 
            </td>
          </tr>
          <tr> 
            <td valign="top"> <span align="left" class="name">Source URL</span></td>
            <td><input name="sourceurl" size="50" value="<?php echo $r->Fields("sourceurl")?>" > 
            </td>
          </tr>
       
          <tr> 
            <td valign="top"><span align="left" class="name">Date</span><br> </td>
            <td valign="top" class="text"> <input type="text" name="date" size="25" value="<?php if ($r->Fields("date")) { echo DateConvertOut($r->Fields("date")) ;} else {echo "00-00-0000";}?>">
			
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

	 
  
<?php		$filelist = AMPfile_list('img/thumb/'); 
		//$img_options = makelistarray($G,'id','galleryname','Select Gallery');
        $galattr= 'onChange="art_showThumb(\'img/thumb/\'+this.value);"';
		$Gal = & new Select('picture',$filelist,$r->Fields("picture"),false,10,null,null,$galattr);
        $th_style = $r->Fields("picture")?null:" style='display:none' ";
        $th_img= '<P><img align="center" width=100 src="http://'.$_SERVER['SERVER_NAME'].'/img/thumb/'.$r->Fields("picture").'" id="active_thumb"'.$th_style.'>';
		echo $buildform->add_row('Image Filename'.$th_img, $Gal);
 	 
 ?>
            <script type="text/javascript"> 
            function art_showThumb(imgname) {
                th_img = document.getElementById('active_thumb');
                th_img.src='http://'+window.location.host+"/"+imgname;
                th_img.style.display="block";
            }
            </script>
          <tr class="text"> 
            <td valign="top"><div align="right"></div></td> 
            <td><p> &nbsp;<a href="imgdir.php" target="_blank">View Images</a> | <a href="#"  onclick="change2('upload');" >Upload Image</a></td>
          </tr><tr><td colspan="2"><div id="upload" style="display:none;"><table width="100%" border="0" align="center"> 
		<?php	echo  addfield('file','Upload New Image <br>(jpg/gif/png files only)','file','','Select image');?>	</table></div>
          </td></tr><tr class="text"> 
            <td valign="top"><div align="right"></div></td> 
            <td>
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
            <td> <input type="textarea" name="piccap" size="50" value="<?php echo htmlspecialchars($r->Fields("piccap"))?>"> 
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
		  <?php		$doc_filelist = AMPfile_list('downloads/'); 
		$Doc = & new Select('doc',$doc_filelist,$r->Fields("doc"));
		echo $buildform->add_row('Document Name', $Doc);
 ?>

          <tr> 
            <td valign="top"></td>
            <td><span class="text"><a href="docdir.php" target="_blank">view 
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
            
			  <a href="#" onclick="new Effect.SlideDown('comment_list');return false;">View /Edit Comments</a>
			  <div id="comment_list" style="display:none;"> 
			  <?php
			  if ($coms->RecordCount() > 0 ){
			  function nicetrim ($s,$MAX_LENGTH) {
				  $s2 = substr($s, 0, $MAX_LENGTH - 3);
				  $s2 .= "...";
				  return $s2;
				}
			  
			   while (!$coms->EOF){
			  	echo '- '.nicetrim($coms->Fields("comment"),5).'<a href="comments.php?id='.$coms->Fields("id").'" target="_new">edit</a><br>';
				$coms->MoveNext();
			  }
			  ?>
			  <br> <br>  <a href="comments.php?action=list&cid=<?php echo $_GET[id] ;?>" target="_blank" class="text">List Page</a>
			  <?php } else { echo "no comments"; }
			  </div>
			  </td>
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
		<tr class="intitle"> 
            <td colspan="2" valign="top">WYSIWYG Settings</td>
          </tr>
          <tr> 
		  
            <td colspan="2" valign="top"><?php
			
			?><a href="#" class="name" onclick="deleteCookie('AMPWYSIWYG'); setCookie('AMPWYSIWYG', 'none'); " >No WYSIWYG Editor</a> | <a href="#" class="name" onclick="deleteCookie('AMPWYSIWYG'); setCookie('AMPWYSIWYG', 'use');" >use WYSIWYG Editor</a></td>
          </tr>  
		  
		  
		  
		  </table></div>
		  <table width = "100%">
          <tr class="intitle"> 
            <td colspan="2" valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><input type="submit" name="<?php if (empty($_GET['id'])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"><?php  if ( AMP_Authorized( AMP_PERMISSION_CONTENT_DELETE ) ){ ?>  <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> 
            <?php }?>
              </td>
          </tr>
        </table>
              
	<input type="hidden" name="MM_recordId" value="<?php echo $id; ?>">
     
	 
</form>

<?php

if ($_GET['id'] or $_GET['vid']) { articleversionlist($id);}

include ("footer.php");

?>
