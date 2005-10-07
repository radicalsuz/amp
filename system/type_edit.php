<?php
$mod_name="content";
require_once("Connections/freedomrising.php"); 
require_once("Connections/sysmenu.class.php");
require_once ('AMP/Content/Section/Contents/Manager.inc.php');
require_once('AMP/Content/Lookups.inc.php');
require_once ('AMP/Content/Labels.inc.php');

// create Menu
$obj = new SysMenu;

  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($_SERVER['QUERY_STRING']) {
    $MM_editAction = $MM_editAction . "?" . $_SERVER['QUERY_STRING'];
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();


  // *** Update Record: set variables
  
if ( ((isset($_REQUEST['MM_update'])) && (isset($_REQUEST['MM_recordId'])) ) or (isset($_REQUEST['MM_insert'])) or ((isset($_REQUEST['MM_delete'])) && (isset($_REQUEST['MM_recordId']))) )  {
  
 if (!$_REQUEST['MM_insert']) { $MM_editRedirectUrl = "edittypes.php";}
    $MM_editTable  = "articletype";
    $MM_editColumn = "id";
    $MM_recordId = "" . $_REQUEST['MM_recordId'] . "";
	$date2 =  DateConvertIn($date2);
    $MM_fieldsStr = "type|value|url|value|image|value|checkbox|value|cap|value|up|value|description|value|uselink|value|linkurl|value|order|value|usetype|value|usenav|value|image2|value|css|value|flash|value|templateid|value|parent|value|listtype|value|date2|value|searchbar|value|secure|value|header|value";
    $MM_columnsStr = "type|',none,''|url|',none,''|image|',none,''|useimage|none,1,0|imgcap|',none,''|up|',none,''|description|',none,''|uselink|none,1,0|linkurl|',none,''|textorder|',none,''|usetype|none,1,0|usenav|none,1,0|image2|',none,''|css|',none,''|flash|',none,''|templateid|none,none,NULL|parent|',none,''|listtype|',none,''|date2|',none,''|searchbar|none,1,0|secure|none,1,0|header|none,1,0";
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
 
 
 if ($MM_insert) {
 $getid=$dbcon->Execute("select id from articletype order by id desc limit 1") or DIE($dbcon->ErrorMsg());
   $MM_editTable  = "articles";
    $MM_editRedirectUrl = "edittypes.php";
	$newsec = $getid->Fields("id");
    $MM_fieldsStr = "type|value|description|value|newsec|value|classxx|value|publishxx|value";
    $MM_columnsStr = "title|',none,''|test|',none,''|type|',none,''|class|none,none,8|publish|none,none,1";
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  }
  ob_end_flush();
   }


?>
<?php
$subtype__MMColParam = "900000000";
if (isset($HTTP_GET_VARS["id"]))
  {$subtype__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
   $subtype=$dbcon->Execute("SELECT * FROM articletype WHERE id = " . ($subtype__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $subtype_numRows=0;
   $subtype__totalRows=$subtype->RecordCount();
    if (isset($id)) {
	$typevar=$subtype->Fields("parent");
	;}
else {$typevar=1;}
   $typelab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$typevar."") or DIE($dbcon->ErrorMsg());
?><?php
//   $Recordset1=$dbcon->Execute("SELECT id, title FROM articles order by title asc") or DIE($dbcon->ErrorMsg());
 //  $Recordset1_numRows=0;
//   $Recordset1__totalRows=$Recordset1->RecordCount();
      $listtypes=$dbcon->Execute("SELECT id, name FROM listtype order by id asc") or DIE($dbcon->ErrorMsg());
   $listtypes_numRows=0;
   $listtypes__totalRows=$listtypes->RecordCount();
?>
<?php
   $templatelab=$dbcon->Execute("SELECT id, name FROM template ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
   $templatelab_numRows=0;
   $templatelab__totalRows=$templatelab->RecordCount();
?>
<?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $subtype_numRows = $subtype_numRows + $Repeat1__numRows;
?><?php include ("header.php");?>
  	  <script type="text/javascript">


function change(which) {
    document.getElementById('main').style.display = 'none';
document.getElementById('advanced').style.display = 'none'; 
    document.getElementById(which).style.display = 'block';
	
    }


</script>

<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form1">
       
		      <table width = "100%" border="0">
          <tr > 
            <td colspan="2" valign="top"  class="banner"> <?php echo helpme(""); ?>Edit Section </td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"> 
        <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
          <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>"></td>
          </tr>
        </table>

  <br><ul id="topnav">
	<li class="tab1"><a href="#" id="a0" onclick="change('main');" >Section Information</a></li>
	
	<li class="tab2"><a href="#" id="a1" onclick="change('advanced');" >Advanced Options </a></li>
</ul>
<div id="main" class="main" style="display: block;">
        <table width="100%" border="0">
          <tr class="intitle"> 
            <td colspan="2"><?php echo helpme("section"); ?>Section Information</td>
          </tr>
          <tr> 
            <td class="name">ID</td>
            <td> <?php echo $subtype->Fields("id")?> </td>
          </tr>
          <tr> 
            <td valign="top" class="red">PUBLISH</td>
            <td><input name="usenav" type="checkbox" id="usenav" value="checkbox" <?php If (($subtype->Fields("usenav")) == "1" or (!$_GET[id])) { echo "CHECKED";} ?>> 
            </td>
          </tr>
          <tr> 
            <td class="name">Section Name</td>
            <td> <input name="type" type="text" id="type" value="<?php echo $subtype->Fields("type")?>" size="50"></td>
          </tr>
          <tr> 
            <td valign="top" class="name">Description</td>
            <td><textarea name="description" cols="45" rows="5" wrap="VIRTUAL" id="description"><?php echo $subtype->Fields("description")?></textarea></td>
          </tr>
          <tr> 
            <td class="name">Subsection Of</td>
            <td> <select name="parent">
                <option value="<?php echo  $typelab->Fields("id")?>" selected><?php echo  $typelab->Fields("type")?></option>
                <?php echo $obj->select_type_tree(0); ?></select></td>
          </tr>
        
          <tr> 
            <td class="name">Section Order</td>
            <td><input name="order" type="text" id="up" value="<?php 
			 echo $subtype->Fields("textorder");	 ?>" size="10" > </td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2"><?php echo helpme("index"); ?>Section Index Page Format</td>
          </tr>
          <tr> 
            <td class="name">List Format</td>
            <td> <!--<select name="listtype" id="listtype">
                <?php
                /*
  if ($listtypes__totalRows > 0){
    $listtypes__index=0;
    $listtypes->MoveFirst();
    WHILE ($listtypes__index < $listtypes__totalRows){
        */
?>
                <OPTION VALUE="<?php #echo  $listtypes->Fields("id")?>"<?php #if ($listtypes->Fields("id")==$subtype->Fields("listtype")) echo "SELECTED";?>> 
                <?php 
				
				
				#echo  $listtypes->Fields("name");?>
                </OPTION>
                <?php
                /*
      $listtypes->MoveNext();
      $listtypes__index++;
    }
    $listtypes__index=0;  
    $listtypes->MoveFirst();
  }
  */
?>
              </select>--> 
              <?php
              print AMP_buildSelect( 'listtype', AMPConstantLookup_Listtypes::instance('listtypes'), $subtype->Fields( 'listtype' ));
              ?>
              </td>

          </tr>
          <tr> 
            <td class="name">Use Section Header</td>
            <td><input name="header" type="checkbox" value="1" <?php If (($subtype->Fields("header")) == "1" or (!$_GET[id])) { echo "CHECKED";} ?>></td>
          </tr>
		
          <tr> 
            <td class="name">Hide Content List</td>
            <td><input name="usetype" type="checkbox" id="usetype2" value="checkbox" <?php If (($subtype->Fields("usetype")) == "1") { echo "CHECKED";} ?>> 
            </td>
          </tr>
          <tr> 
            <td class="name">Content List Repeats</td>
            <td><input name="up" type="text" id="up" value="<?php 
			if (($subtype->Fields("up")) == NULL) {
			echo "20";}
					 else  {
			 echo $subtype->Fields("up");}	 ?>" size="10" > </td>
          </tr>
     </table>
		  </div>
		<div id="advanced"  style="display: none;">
		  <table width="100%" border="0">
          <tr class="intitle"> 
            <td colspan="2"><?php echo helpme("link"); ?>Advanced Options</td>
          </tr>
		
          <tr> 
            <td class="name">Other URL to link to</td>
            <td class="name"> <input type="text" name="linkurl" size="45" value="<?php echo $subtype->Fields("linkurl")?>"> 
              <br> <input name="uselink" type="checkbox" id="uselink" value="checkbox" <?php If (($subtype->Fields("uselink")) == "1") { echo "CHECKED";} ?>>
              Link to above URL </td>
          </tr>
		    <tr> 
            <td class="name">Date</td>
            <td class="text"><input type="text" name="date2" size="25" value="<?php echo DateConvertOut($subtype->Fields("date2"))?>">
              (12-30-2002) </td>
          </tr>
		    <tr> 
            <td class="name">Override Default Section Header with Content ID#</td>
            <td><input name="url" type="text" id="url" value="<?php 
			
				
			 echo $subtype->Fields("url");	 ?>" size="10" > </td>
          </tr>
		         <tr> 
            <td class="name">Show List Search Bar</td>
            <td><input name="searchbar" type="checkbox" id="searchbar" value="1" <?php If (($subtype->Fields("searchbar")) == "1") { echo "CHECKED";} ?>></td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2"><?php echo helpme("images"); ?>Images</td>
          </tr>
          <tr> 
            <td class="name">Image for navigation component</td>
            <td> <input type="text" name="image" size="50" value="<?php echo $subtype->Fields("image")?>"> 
            </td>
          </tr>
          <tr> 
            <td class="name">&nbsp;</td>
            <td class="name"> <input <?php If (($subtype->Fields("useimage")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox" value="checkbox">
              Use image instead of section name </td>
          </tr>
          <tr> 
            <td class="name">Small Section Image(for index pages, <br>
              provide full path)</td>
            <td> <input type="text" name="image2" size="50" value="<?php echo $subtype->Fields("image2")?>"> 
            </td>
          </tr>
          <tr> 
            <td class="name">Banner Image</td>
            <td><input name="flash" type="text" id="flash" value="<?php echo $subtype->Fields("flash")?>" size="45" ></td>
          </tr>
          <tr> 
            <td class="name">Image 2 Caption (not used)</td>
            <td> <textarea name="cap" wrap="VIRTUAL" cols="45" rows="3"><?php echo $subtype->Fields("imgcap")?></textarea></td>
          </tr><tr class="intitle"> 
            <td colspan="2"><?php echo helpme("protect"); ?>Section Security</td>
          </tr>
          <tr> 
            <td class="name">Require login to view section</td>
            <td><input name="secure" type="checkbox" id="secure" value="1" <?php If (($subtype->Fields("secure")) == "1") { echo "CHECKED";} ?>> 
            </td>
          </tr>
          <tr class="intitle"> 
            <td colspan="2"><?php echo helpme("style"); ?>Style Features </td>
          </tr>
          <tr> 
            <td class="name">Different css file to use</td>
            <td> <input name="css" type="text" value="<?php echo $subtype->Fields("css")?>" size="45" ><BR><span class="name">(use commas to include multiple files)</span> 
            </td>
          </tr>
          <tr> 
            <td class="name">Template</td>
            <td><select name="templateid" id="templateid">
                <option value="">none</option>
                <?php
  if ($templatelab__totalRows > 0){
    $templatelab__index=0;
    $templatelab->MoveFirst();
    WHILE ($templatelab__index < $templatelab__totalRows){
?>
                <option value="<?php echo  $templatelab->Fields("id")?>"<?php if ($templatelab->Fields("id")==$subtype->Fields("templateid")) echo "SELECTED";?>> 
                <?php echo  $templatelab->Fields("name");?> </option>
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
        </table></div>
		
		 <table width = "100%" border="0">
          <tr class="intitle"> 
            <td colspan="2" valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2" valign="top"><input type="submit" name="<?php if (empty($HTTP_GET_VARS["id"])== TRUE) { echo "MM_insert";} else {echo "MM_update";} ?>" value="Save Changes"> 
        <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
          <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>"></td>
          </tr>
        </table>
		
                
              <p><a href="nav_position.php?type=<?php echo $HTTP_GET_VARS["id"]; ?>"> 
                Edit Navigation Files for Lists</a>
				<br>
          <a href="nav_position.php?typeid=<?php echo $HTTP_GET_VARS["id"]; ?>"> 
          Edit Navigation Files for Content</a></p>
              <p> 
         
</form>
  
      <?php include ("footer.php");
?>
