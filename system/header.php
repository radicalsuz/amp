<?php 
$cookiename = "AMPheader";
if ($_COOKIE[$cookiename]) {$cookvalue = $_COOKIE[$cookiename]; 
}
else { $cookvalue = "standard";}
if  ($cookvalue == "standard") {
	$hd_standard = "block";
	$hd_basic = "none";
	}
else {
	$hd_standard = "none";
	$hd_basic = "block";
	}




   $headernav=$dbcon->Execute("SELECT name, id, file, perid  FROM modules where publish=1 order by name asc") or DIE($dbcon->ErrorMsg());
 if ($modid ==NULL) {$modid =19;}
 $headerinst=$dbcon->Execute("SELECT *  FROM modules where id = $modid") or DIE($dbcon->ErrorMsg());
   $headernav_numRows=0;
    $headernav_numRows3=0;
   $headernav__totalRows=$headernav->RecordCount();
    $headernav__totalRows3=$headernav->RecordCount();
$browser_ie =  strstr(getenv('HTTP_USER_AGENT'), 'MSIE') ;
$browser_win =  strstr(getenv('HTTP_USER_AGENT'), 'Win') ;
$browser_mo =  strstr(getenv('HTTP_USER_AGENT'), 'Mozilla/5') ;
if (!$_GET[noHeader]) {
 ?>
<html>
<head>
<title><?php echo $SiteName  ; ?> Administration</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="managment.css" type="text/css">
		  	  <script type="text/javascript">
			function getCookie(name)
{
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1)
    {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1)
    {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}
  
function setCookie(name, value, expires, path, domain, secure)
{
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

function setPointer(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor)
{
    var theCells = null;

    // 1. Pointer and mark feature are disabled or the browser can't get the
    //    row -> exits
    if ((thePointerColor == '' && theMarkColor == '')
        || typeof(theRow.style) == 'undefined') {
        return false;
    }

    // 2. Gets the current row and exits if the browser can't get it
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }

    // 3. Gets the current color...
    var rowCellsCnt  = theCells.length;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    // 3.1 ... with DOM compatible browsers except Opera that does not return
    //         valid values with "getAttribute"
    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[0].getAttribute('bgcolor');
        domDetect    = true;
    }
    // 3.2 ... with other browsers
    else {
        currentColor = theCells[0].style.backgroundColor;
        domDetect    = false;
    } // end 3

    // 3.3 ... Opera changes colors set via HTML to rgb(r,g,b) format so fix it
    if (currentColor.indexOf("rgb") >= 0)
    {
        var rgbStr = currentColor.slice(currentColor.indexOf('(') + 1,
                                     currentColor.indexOf(')'));
        var rgbValues = rgbStr.split(",");
        currentColor = "#";
        var hexChars = "0123456789ABCDEF";
        for (var i = 0; i < 3; i++)
        {
            var v = rgbValues[i].valueOf();
            currentColor += hexChars.charAt(v/16) + hexChars.charAt(v%16);
        }
    }

    // 4. Defines the new color
    // 4.1 Current color is the default one
    if (currentColor == ''
        || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
        if (theAction == 'over' && thePointerColor != '') {
            newColor              = thePointerColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
            // Garvin: deactivated onclick marking of the checkbox because it's also executed
            // when an action (like edit/delete) on a single item is performed. Then the checkbox
            // would get deactived, even though we need it activated. Maybe there is a way
            // to detect if the row was clicked, and not an item therein...
            // document.getElementById('id_rows_to_delete' + theRowNum).checked = true;
        }
    }
    // 4.1.2 Current color is the pointer one
    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newColor              = theDefaultColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
            // document.getElementById('id_rows_to_delete' + theRowNum).checked = true;
        }
    }
    // 4.1.3 Current color is the marker one
    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {
            newColor              = (thePointerColor != '')
                                  ? thePointerColor
                                  : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                                  ? true
                                  : null;
            // document.getElementById('id_rows_to_delete' + theRowNum).checked = false;
        }
    } // end 4

    // 5. Sets the new color...
    if (newColor) {
        var c = null;
        // 5.1 ... with DOM compatible browsers except Opera
        if (domDetect) {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].setAttribute('bgcolor', newColor, 0);
            } // end for
        }
        // 5.2 ... with other browsers
        else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    } // end 5

    return true;
} // end of the 'setPointer()' function



function deleteCookie(name)
{
    if (getCookie(name))
    {
        document.cookie = name + "=" + 
           "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}



function changex(which) {
    document.getElementById('standard').style.display = 'none';
document.getElementById('basic').style.display = 'none'; 
    document.getElementById(which).style.display = 'block';
	
    }


	function hideClass(theclass, objtype) {
	if (objtype=='') {objtype='div';}
	var objset=document.getElementsByTagName(objtype);
	for (i=0;i<objset.length; i++) {
		if (objset.item(i).className == theclass){
			objset.item(i).style.display = 'none';
		}
	}
	
	}

	function showClass(theclass, objtype) {
	if (objtype=='') {objtype='div';}
	for (i=0;i<document.getElementsByTagName(objtype).length; i++) {
		if (document.getElementsByTagName(objtype).item(i).className == theclass){
			document.getElementsByTagName(objtype).item(i).style.display = 'block';
		}
	}
	}

	function change_any(which, whatkind) {
	if (whatkind!='') {hideClass(whatkind, '');}
		if(document.getElementById(which).style.display == 'block' ) {
			document.getElementById(which).style.display = 'none';
		} else {
		document.getElementById(which).style.display = 'block';
		}
	}
	
</script>
<script type="text/javascript" src="Connections/popcalendar.js"></script>
<script language="JavaScript" src="../Connections/functions.js"></script>

<!--  <script language="JavaScript"> 

function confirmSubmit(text) { 
  var yes = confirm(text); 
  if (yes) return true; 
  else return false; 
} 

</script>  
// --> 


</head>

<body bgcolor="#ffffff" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" <?php  if ($browser_mo) { echo "onload=\"initEditor()\" " ;} ?>>
<br><table border="0" cellpadding="0" cellspacing="0" width="720" align="center"> 
<tr bordercolor="#FFFFFF" bgcolor="#006699" valign="top">
     <td colspan="4" class="pagetitle">
 
       <table width="720" border="0">
      <tr> 
        <td rowspan="2" bgcolor="#006699"><span class="toptitle"><a href="<?php echo $Web_url ; ?>" class="toptitle"><?php echo $SiteName ; ?></a> 
          <br>
          Administration</span> </td>
        <td align="right" valign="bottom" bgcolor="#006699" class="toplinks"><b class="toplinks"> 
          <a href="index.php" class="toplinks" >ADMIN HOME</a> : <a href="http://www.radicaldesigns.org/manual.pdf" target="_blank" class="toplinks">HELP 
          : </a> <a href="html.html" target="_blank" class="toplinks">HTML TIPS</a> 
          : <a href="logout.php" class="toplinks">LOGOUT</a></b></font><br>
Navigation Display:&nbsp;&nbsp;&nbsp; <a href="#" onclick="changex('basic'); deleteCookie('<?php echo $cookiename ?>'); setCookie('<?php echo $cookiename ?>', 'basic'); " class="toplinks" >Basic</a> | <a href="#" id="a1" onclick="changex('standard') ;deleteCookie('<?php echo $cookiename ?>'); setCookie('<?php echo $cookiename ?>', 'standard');" class="toplinks">Advanced</a></td>
      </tr>
      <tr>
                       <td align="right" valign="bottom" bgcolor="#006699" class="toplinks">&nbsp;&nbsp;<select onChange="MM_jumpMenu('parent',this,0)" name="modid" id="modid">
                <option value="index.php">Select Module</option>
				 <option value="index.php">&nbsp;&nbsp;---------</option>
                <?php
  if ($headernav__totalRows > 0){
    $headernav__index=0;
    $headernav->MoveFirst();
    WHILE ($headernav__index < $headernav__totalRows){

             $perid=$headernav->Fields("perid");
			   if ($userper["$perid"] == 1) { ?>  <option value="<?php echo  $headernav->Fields("file");?>"> 
                <?php echo  $headernav->Fields("name");?> </option>
                <?php 
		}
      $headernav->MoveNext();
      $headernav__index++;
    }
    $headernav__index=0;  
    $headernav->MoveFirst();
  }
?>
              </select></td>
      </tr>
    </table>
      </td>
  </tr>
  <tr> 
    <td bgcolor="#CCCCCC" width="160" valign="top"> <table width="160" border="0" cellspacing="10" cellpadding="0">
        <tr>
          <td valign="top">
	
	     <?php $perid=$headerinst->Fields("perid");
			   if ($userper["$perid"] == 1 && $modid != 19) { ?> 
                    <p class="sidetitle"><?php echo  $headerinst->Fields("name");?>
					<p class="side">  
				    <?php echo  evalhtml($headerinst->Fields("navhtml"));?>
				     </p>
				
          <?php 		}?>
		   <div id="standard" style="display: <?php echo $hd_standard ?>;">
          <?php if ($userper[10] == 1){{} ?>
          <p align="center" class="banner"><font size="-3">CONTENT 
      SYSTEM</font></p>
	  <p class="sidetitle">Home Page</p>
	  <a href="article_list.php?&class=2" class="side">View/Edit Homepage </a> <br>
   
	  <a href="article_fpedit.php" class="side">Add Homepage Content</a> <br>
          <a href="module_nav_edit.php?id=2" class="side">Home Page Navigation</a><br>
          <a href="article_list.php?&fpnews=1" class="side"> Homepage News</a> <br>
          <p class="sidetitle">Content</p>
            
           
            
    <?php if ($userper[1] == 1){{} ?>
    <a href="articlelist.php" class="side">View/Edit Content </a> <br>
             
            <?php if ($userper[1] == 1){}} ?>
            <?php if ($userper[2] == 1){{} ?>
            <a href="article_edit.php" class="side">Add Content</a><br>
			<a href="module_nav_edit.php?id=1" class="side">Content Navigation</a><br>
            <?php if ($userper[2] == 1){}} ?>
			<?php if ($userper[85] == 1){{} ?>
    <p class="sidetitle">Docs and Images
    <p class="side"> 
     
      <a href="docdir.php" class="side">View Documents</a><br>
    <a href="doc_upload.php" class="side">Upload Documents</a><br>
       <a href="imgdir.php" class="side">View Images</a><br>
    <a href="imgup.php" class="side">Upload Images</a>
    </p>
    <?php if ($userper[85] == 1){}} ?>
          <p class="sidetitle">Sections</p>
             <?php if ($userper[9] == 1){{} ?>
            <a href="edittypes.php" class="side">View/Edit Sections</a><br>
			<?php if ($userper[9] == 1){}} ?>
			<?php if ($userper[4] == 1){{} ?>
            <a href="type_edit.php" class="side">Add Section</a><br>
			<?php if ($userper[4] == 1){}} ?>
			<?php if ($userper[8] == 1){{} ?>   
				<a href="class.php" class="side">Add Class</a><br>
          <?php if ($userper[8] == 1){}} ?></P>
          <?php if ($userper[10] == 1){}} ?>
          <?php if ($userper[53] == 1){{} ?>
          <p align="center" class="banner"><font size="-3">MODULE SYSTEM</font></p>
          <select onChange="MM_jumpMenu('parent',this,0)" name="modid" id="modid">
                <option value="index.php">Select Module</option>
				 <option value="index.php">&nbsp;&nbsp;---------</option>
                <?php
  if ($headernav__totalRows3 > 0){
    $headernav__index3=0;
    $headernav->MoveFirst();
    WHILE ($headernav__index3 < $headernav__totalRows3){

             $perid=$headernav->Fields("perid");
			   if ($userper["$perid"] == 1) { ?>  <option value="<?php echo  $headernav->Fields("file");?>"> 
                <?php echo  $headernav->Fields("name");?> </option>
                <?php 
		}
      $headernav->MoveNext();
      $headernav__index3++;
    }
    $headernav__index3=0;  
    $headernav->MoveFirst();
  }
?>
             </select>
          <p class="side"> 
		  	
	  <?php if ($userper[45] == 1){{} ?>
      <a href="moduletext_list.php" class="side">Edit Module Intro Text</a><br>
      <?php if ($userper[45] == 1){}} ?>
      <?php if ($userper[46] == 1){{} ?>
      <a href="moduletext_edit.php" class="side">Add Module Intro Text</a> <br>
      <?php if ($userper[46] == 1){}} ?>
      <?php if ($userper[54] == 1){{} ?>
      <a href="modfields_list.php" class="side">Edit User Data Modules</a><br>
	  <a href="modinput4_list.php" class="side">Edit NEW UDMs</a><br>
      <?php if ($userper[54] == 1){}} ?>
      <?php if ($userper[55] == 1){{} ?>
	 <a href="udmwizard.php" class="side">Add User Data Module</a><br>
	 <a href="modinput4_new.php" class="side">Add NEW UDM</a><br>
      <?php if ($userper[55] == 1){}} ?>
	    <a href="module_edit.php" class="side">Add Module</a> <br>
		  <a href="module_list.php" class="side">Edit Module Settings</a><br>
	

    </p>
          <?php if ($userper[53] == 1){}} ?>
          <?php if ($userper[44] == 1){{} ?>
          <p align="center" class="banner"><font size="-3">NAVIGATION SETTINGS</font></p>
          <?php if ($userper[47] == 1){{} ?>
          <a href="nav_list.php?nons=1" class="side">View/Edit Basic Nav Files</a><br>
<a href="nav_list.php" class="side">View/Edit All Nav Files</a><br>
          <?php if ($userper[47] == 1){}} ?>
          <?php if ($userper[48] == 1){{} ?>
          <a href="nav_minedit.php" class="side">Add Basic Nav File</a><br>
		  <a href="nav_edit.php" class="side">Add Dynamic Nav File</a><br>
		   <a href="hotwords.php" class="side">Edit/Add HotWord</a><br>
      <?php if ($userper[48] == 1){}} ?>
	   <p align="center" class="banner"><font size="-3">TEMPLATE SETTINGS 
            </font></p>
      <?php if ($userper[49] == 1){{} ?>
      <a href="template_list.php" class="side">View/Edit Design Template</a><br>
	  <a href="template_edit3.php" class="side">Add Design Template</a><br>
	  	  <a href="css_edit.php" class="side">Edit CSS</a><br>
		  <a href="css_list.php" class="side">Edit Custom CSS</a><br>
		  

      <?php if ($userper[49] == 1){}} ?>
      <?php if ($userper[50] == 1){{} ?>
      
      <?php if ($userper[50] == 1){}} ?>
	  <p align="center" class="banner"><font size="-3">SYSTEM SETTINGS 
            </font></p>
      <?php if ($userper[77] == 1){{} ?>
      <a href="permissions_list.php" class="side">System Permisssions</a><br>
      <?php if ($userper[77] == 1){}} ?>
      <?php if ($userper[51] == 1){{} ?>
      <a href="user_list.php" class="side">System Users</a><br>
      <?php if ($userper[51] == 1){}} ?>
      <?php if ($userper[52] == 1){{} ?>
	  <a href="redirect.php?action=list" class="side">Page Redirection</a><br>
      <a href="sysvar.php" class="side">System Settings</a><br> 
      <a href="wizard_setup.php" class="side">Setup Wizard</a><br>
      <a href="rssfeed.php?action=list" class="side">RSS Feeds</a><br>
	  <?php if ($userper[52] == 1){}} ?>
    <a href="flushcache.php" class="side">Reset Cahce</a><br>
	<!--<a href="protect.php" class="side">Add Redirect</a><br> -->
          <?php if ($userper[73] == 1){{} ?>
          <a href="../contacts/" class="side">CONTACT SYSTEM</a><br> 
          <?php if ($userper[73] == 1){}} ?></b>
          <?php if ($userper[44] == 1){}} ?>
		  </div>
		   <div id="basic"  style="display: <?php echo $hd_basic ?>;">
		             <?php if ($userper[10] == 1){{} ?>
          <p align="center" class="banner"><font size="-3">CONTENT</font></p>
	      
         
            
           
            
          <?php if ($userper[2] == 1){{} ?>
          <a href="article_edit.php" class="side">Add Content</a><br>
          <?php if ($userper[2] == 1){}} ?>
<?php if ($userper[1] == 1){{} ?>
    <a href="articlelist.php" class="side">View/Edit Content </a> <br>
          
			
	      <a href="article_fpedit.php" class="side">Add Homepage Content</a> <a href="article_list.php?&class=2" class="side">View/Edit Homepage </a>  <?php if ($userper[1] == 1){}} ?> <br>
	 
            <p class="sidetitle">Sections</p>
             <?php if ($userper[4] == 1){{} ?>
             <a href="type_edit.php" class="side">Add Section</a><br>
             <?php if ($userper[4] == 1){}} ?>
<?php if ($userper[9] == 1){{} ?>
            <a href="edittypes.php" class="side">View/Edit Sections</a><br>
			<?php if ($userper[9] == 1){}} ?>
			<p class="sidetitle">
      <?php if ($userper[85] == 1){{} ?>
      Docs and Images
    <p class="side"> 
     
      <a href="docdir.php" class="side">View Documents</a><br>
    <a href="doc_upload.php" class="side">Upload Documents</a><br>
       <a href="imgdir.php" class="side">View Images</a><br>
    <a href="imgup.php" class="side">Upload Images</a>
    </p>
    <?php if ($userper[85] == 1){}} ?>
          
          
          <p align="center" class="banner"><font size="-3">MODULEs</font></p>
          <select onChange="MM_jumpMenu('parent',this,0)" name="modid" id="modid">
                <option value="index.php">Select Module</option>
				 <option value="index.php">&nbsp;&nbsp;---------</option>
                <?php
  if ($headernav__totalRows3 > 0){
    $headernav__index3=0;
    $headernav->MoveFirst();
    WHILE ($headernav__index3 < $headernav__totalRows3){

             $perid=$headernav->Fields("perid");
			   if ($userper["$perid"] == 1) { ?>  <option value="<?php echo  $headernav->Fields("file");?>"> 
                <?php echo  $headernav->Fields("name");?> </option>
                <?php 
		}
      $headernav->MoveNext();
      $headernav__index3++;
    }
    $headernav__index3=0;  
    $headernav->MoveFirst();
  }
?>
             </select>
      <?php if ($userper[10] == 1){}} ?>
		   </div>
        </td>
        </tr>
      </table> </td>
    
     
    <td valign="top" bgcolor="#FFFFFF" width="560">
	
	<? } ?>