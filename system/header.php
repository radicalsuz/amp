<?php 
include("Connections/system_navs.php");
$cookiename = "AMPheader";
if (isset($_COOKIE[$cookiename]) && $_COOKIE[$cookiename]) {
	$cookvalue = $_COOKIE[$cookiename]; 
} else { 
	$cookvalue = "standard";
}

if  ($cookvalue == "standard") {
	$hd_standard = "block";
	$hd_basic = "none";
} else {
	$hd_standard = "none";
	$hd_basic = "block";
}

//ENSURE THAT THE current user is allowed to see this page
$MM_current_page =basename($_SERVER['PHP_SELF']);
$MM_query_string = '';
if (isset($_SERVER['QUERY_STRING'])) {
	parse_str($_SERVER['QUERY_STRING'], $MM_active_vars);
	foreach ($MM_active_vars as $v_key=>$v_value) {
		$MM_query_string.=$v_key."=".$v_value."&";
	}
	$MM_current_page.="?".$MM_query_string;
}

/* Disabling permissions system until this actually makes sense (i.e., we have
** a source for ID and so on...
*
$allowed_pages=$dbcon->GetAssoc("Select id, system_allow_only from users where id=$ID");
if (isset($allowed_pages[$ID])&&$allowed_pages[$ID]!='') { //user is restricted to certain pages
	$permit_access=FALSE;
	$allowed_pageset=split(",", $allowed_pages[$ID]);
	foreach ($allowed_pageset as $key=>$allowed_page) {
		$allowed_page=trim($allowed_page);
		if (strlen($allowed_page)>3) {
			#print $MM_current_page."   ".$allowed_page;
			if (substr($MM_current_page, 0, strlen($allowed_page))==$allowed_page) {
				$permit_access=TRUE;
			}
		}
	}
	if (!$permit_access) {
		header ("Location:index.php");
	}
}
*/

$headernav = $dbcon->Execute("SELECT name, id, file, perid  FROM modules where publish=1 order by name asc") or DIE("Couldn't fetch nav header info: " . $dbcon->ErrorMsg());


#$nav_link .= '<div width= "100%"><fieldset   style="  border: 1px solid black;">';
//$nav_link .= 'test';
//$nav_link .= '<ul id="topnav">';
#while (!$mod_navs->EOF) {
#	$nav_link .= '<span class="option"><a href="'.$mod_navs->Fields("url").'">'.$mod_navs->Fields("name").'</a></span>';
#	$mod_navs->MoveNext();
#}
#if ($headerinst->Fields("userdatamod") == 1) {
#	$nav_link .= '<span class="option"><a href="modinput4_data.php?modin='.$headerinst->Fields("userdatamodid").'">View/Edit</a></span>';
#	$nav_link .= '<span class="option"><a href="modinput4_view.php?modin='.$headerinst->Fields("userdatamodid").'">Add</a></span>';
#	$nav_link .= '<span class="option"><a href="modinput4_edit.php?modin='.$headerinst->Fields("userdatamodid").'">Data Settings</a></span>';
#}
#if ($modid != 19) {
#	$nav_link .= '<span class="option"><a href="module_control_list.php?modid='.$modid.'">Settings</a></span>';
#}
//$nav_link .= '</ul>';
#$nav_link .= '<br clear="all" />'; 

function nav_css($class=NULL) {
	$output= ' class="side_'.$class.'"';  
	if (!$class) {$output=' class="side_type"'; }
	return $output;
}
// get information about the module
if (isset($modid) && $modid !=NULL) {
	$headerinst = $dbcon->Execute("SELECT * FROM modules where id=" . $dbcon->qstr($modid)) or DIE("could not load module information in header: " . $dbcon->ErrorMsg());
	$mod_navs = $dbcon->Execute("SELECT * FROM module_navs where module_id=" . $dbcon->qstr($modid)) or DIE("could not load module navigation information in header".$dbcon->ErrorMsg());
	$header_title = $headerinst->Fields("name");
	$header_udm = $headerinst->Fields("userdatamod");
	$header_udmid = $headerinst->Fields("userdatamodid");
	$mod_name =$modid;
}

$nav_link = '';
if (!isset($mod_name)) $mod_name = '';

if (isset($sys_nav[$mod_name])) {

    $modsize= sizeof($sys_nav[$mod_name]);

    if (isset($sys_nav[$mod_name]['title']) && $sys_nav[$mod_name]['title']) {
        $header_title = $sys_nav[$mod_name]['title'];
        $modsize = ($modsize -1);
    }

    $nav_link .= "<p class='side_banner'>".$header_title."</p>";
    $nav_link .= "\n	<ul class=side>";

    for ($x=0; $x<$modsize; $x++) {
        if (isset($sys_nav[$mod_name][$x]['title'])) {
            $nav_link .= "\n	</ul>\n<p class ='sidetitle'>".$sys_nav[$mod_name][$x]['title']."</p>\n	<ul class=side>";
        } else {
            $nav_css = '';
            if (isset($sys_nav[$mod_name][$x]['class'])) {
                $nav_css = nav_css($sys_nav[$mod_name][$x]['class']);
            }
            $nav_link .= "\n		<li $nav_css><a href='".$sys_nav[$mod_name][$x]['link']."' >".$sys_nav[$mod_name][$x]['name']."</a></li>";
        }
    }

}

if (isset($header_udm) && $header_udm == 1) {
    if (!isset($sys_nav[$mod_name])) {
        $nav_link .= '<p class="side_banner">' . $headerinst->Fields('name') . '</p>';
        $nav_link .= "\n    <ul class=side>";
    }
	$nav_link .= "\n		<li ".nav_css("view")."><a href='modinput4_data.php?modin=".$header_udmid."' >View/Edit Data</a></li>";
	$nav_link .= "\n		<li ".nav_css("add")."><a href='modinput4_view.php?modin=".$header_udmid."' >Add Data</a></li>";
	$nav_link .= "\n		<li ".nav_css("search")." ><a href='modinput4_search.php?modin=".$header_udmid."' >Search Data</a></li>";	
	$nav_link .= "\n		<li ".nav_css("form")." ><a href='modinput4_edit.php?modin=".$header_udmid."' >Form Settings</a></li>";
	$nav_link .= "\n		<li ".nav_css("add")."><a href='modinput4_copy.php?modin=".$header_udmid."' >Copy Form</a></li>";
	//$nav_link .= "\n		<li ".nav_css("add")."><a href='modinput4_copy.php?modin=".$header_udmid."' >Copy Form Template</a></li>";
}
if (isset($modid) && $modid != 19 && $modid != 31 && $modid != 30 && $modid) {
	$nav_link .= "\n		<li ".nav_css("page")."><a href='module_header_list.php?modid=".$modid."' >Public Pages</a></li>";
	$nav_link .= "\n		<li ".nav_css("settings")."><a href='module_control_list.php?modid=".$modid."' >Module Settings</a></li>";
}

if ($nav_link) {
    $nav_link .= "\n	</ul>";
    $nav_link .= "<br clear='all' />"; 
}

if (!isset($_GET['noHeader']) || !$_GET['noHeader']) {

    global $SystemSettings;

    $encoding = (isset($SystemSettings['encoding'])) ? $SystemSettings['encoding'] : 'iso-8859-1';

?>
<html>
<head>
<title><?= $SiteName  ; ?> Administration</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?= $encoding ?>">
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
	if (!objtype>'') {objtype='div';}
	var objset=document.getElementsByTagName(objtype);
	for (i=0;i<objset.length; i++) {
		if (objset.item(i).className == theclass){
			objset.item(i).style.display = 'none';
		}
	}
	
}

function showClass(theclass, objtype) {
	if (!objtype>'') {objtype='div';}
	var objset=document.getElementsByTagName(objtype);
	for (i=0;i<objset.length; i++) {
		if (objset.item(i).className == theclass){
			objset.item(i).style.display = 'block';
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
	
function showUploadWindow (parentform, calledfield, dtype) {
    url  = 'http://'+location.host+'/upload_popup.php?pform='+parentform+'&pfield='+calledfield;
    if (dtype) url = url + '&doctype='+dtype;
    hWnd = window.open( url, 'recordWindow', 'height=175,width=300,scrollbars=no,menubar=no,toolbar=no,resizeable=no,location=no,status=no' );
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
<style>
	.top {align:right; font-size:10px; }
	.subfield {background-color: #FFFFFF;}
legend {border: 1px solid black;  border-top: none; background-color: #eee; padding: 0 1ex; }

    .option  {
      float:left;
      background:url("images/norm_left.gif") no-repeat left top;
      margin:0;
      padding:0 0 0 9px;
      }
    .option a {
      float:left;
      display:block;
      background:url("images/norm_right.gif") no-repeat right top;
      padding:5px 15px 4px 6px;
      text-decoration:none;
      font-weight:bold;
            font-size: 80%;
      color:#765;
      }
    /* Commented Backslash Hack
       hides rule from IE5-Mac \*/
    .option a {float:none;}
    /* End IE5-Mac hack */
    .option a:hover {
      color:#333;
      }

    .option.current {
      background-image:url("images/norm_left_on.gif");
      border-width:0;
      }

    .option.current a {
      background-image:url("images/norm_right_on.gif");
      color:#333;
      }


</style>
<meta http-equiv="Content-Type" content="text/html; charset=<?= $encoding ?>">

    <?php include("Connections/ddnav.php");
    ?>
</head>

<body <?= (isset($browser_mo) && $browser_mo) ? 'onload="initEditor()"' : '' ?>>
    <table cellpadding="0" cellspacing="0" width="100%" align="center"> 
        <tr bordercolor="#FFFFFF" bgcolor="#dedede" valign="top">
            <td colspan="4" id="pagetitle">
 
                <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#006699">
                    <tr id="header"> 
                        <td><nobr><img src="images/amp-megaphone.png" align = middle style="padding-right:15px"><span class="toptitle"><a href="<?php echo $Web_url ; ?>" class="toptitle"><?php echo $SiteName ; ?></a> Administration</span></nobr> </td>
                        <td align="right" valign="middle" bgcolor="#006699" class="toplinks"> 
        
<p class = "toplinks">User: <?php echo $_SERVER['REMOTE_USER']; ?>&nbsp;&nbsp;&nbsp; <a href="logout.php"  class="toplinks" >Logout</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p></td>
      </tr>
      <tr><td id="navlinks" colspan="2">&nbsp;</td></tr>
    </table>
  </td>
</tr>
<tr> 
    <td bgcolor="#dedede" width="160" valign="top"> 
	<?= $nav_link ?>
	     <?php // $perid=$headerinst->Fields("perid");
			   //if ($userper["$perid"] == 1 && $modid != 19) { }?>
          <?php // if ($userper[10] == 1){{} ?>
          <?php //if ($userper[53] == 1){{} ?>
      
<br/><br/>
<img src ="images/spacer.gif" width = "165" height="1">
         </td>
    
     
    <td valign="top" bgcolor="#FFFFFF" width="100%">
	<div><fieldset  style=" border: 1px solid grey; margin:20px; padding-top:10px; padding-left:10px; padding-right:10px; padding-bottom:10px;">
	
	
	<? 
			
	
} ?>
