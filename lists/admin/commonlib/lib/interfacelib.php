<?
require_once "accesscheck.php";

# interface functions

class WebblerListing {
  var $title;
  var $elements = array();
  var $columns = array();
  var $buttons = array();

  function WebblerListing($title) {
  	$this->title = $title;
  }

  function addElement($name,$url = "",$colsize="") {
  	if (!isset($this->elements[$name])) {
      $this->elements[$name] = array(
        "name" => $name,
        "url" => $url,
        "colsize" => $colsize,
        "columns" => array(),
      );
    }
  }

  function deleteElement($name) {
  	unset($this->elements[$name]);
  }

  function addColumn($name,$column_name,$value,$url="",$align="") {
  	if (!isset($name))
    	return;
  	$this->columns[$column_name] = $column_name;
    $this->elements[$name]["columns"]["$column_name"] = array(
    	"value" => $value,
      "url" => $url,
      "align"=> $align,
    );
  }

  function addInput ($name,$value) {
  	$this->addElement($name);
    $this->addColumn($name,"value",
    	sprintf('<input type=text name="%s" value="%s" size=40 class="listinginput">',
      strtolower($name),$value));
  }

	function addButton($name,$url) {
		$this->buttons[$name] = $url;
	}

  function listingStart() {
    return '<table cellpadding="0" cellspacing="0" border="0" width="536">';
  }

  function listingHeader() {
    $html .= '<tr valign="top">';
    $html .= sprintf('<td><a name="%s"><span class="listinghdname">%s</span></a></td>',strtolower($this->title),$this->title);
    foreach ($this->columns as $column) {
      $html .= sprintf('<td><span class="listinghdelement">%s</span></td>',$column);
    }
  #  $html .= sprintf('<td align="right"><span class="listinghdelementright">%s</span></td>',$lastelement);
    $html .= '</tr>';
    return $html;
  }

  function listingElement($element) {
    if ($element["colsize"])
      $width = 'width='.$element["colsize"];
    else
      $width = "";
    $html = '<tr valign="middle">';
    if ($element["url"]) {
      $html .= sprintf('<td valign="top" %s class="listingname"><span class="listingname"><a href="%s" class="listingname">%s</a></span></td>',$width,$element["url"],$element["name"]);
    } else {
      $html .= sprintf('<td valign="top" %s class="listingname"><span class="listingname">%s</span></td>',$width,$element["name"]);
    }
    foreach ($this->columns as $column) {
      if ($element["columns"][$column]["value"]) {
      	$value = $element["columns"][$column]["value"];
      } else {
      	$value = $column;
      }
      if ($element["columns"][$column]["align"]) {
      	$align = $element["columns"][$column]["align"];
      } else {
      	$align = '';
      }
      if ($element["columns"][$column]["url"]) {
        $html .= sprintf('<td valign="top" class="listingelement%s"><span class="listingelement%s"><a href="%s" class="listingelement">%s</a></span></td>',$align,$align,$element["columns"][$column]["url"],$value);
      } else {
        $html .= sprintf('<td valign="top" class="listingelement%s"><span class="listingelement%s">%s</span></td>',$align,$align,$element["columns"][$column]["value"]);
      }
    }
#  $html .= sprintf('<td align="right"><span class="listingelementright">%s</span></td>',$lastelement);
    $html .= '</tr>';
    /*
    $html .= <td><a class="branches" href="">title</a></td>
  <td align="left">text box</td>
  <td align="right"><input type="Text" name="listorder" value="1" class="listorder" size="1"></td>
  </tr>
    */
    $html .= sprintf('<!--greenline start-->
      <tr valign="middle">
      <td colspan="%d" bgcolor="#CCCC99"><img height=1 alt="" src="images/transparent.png" width=1 border=0></td></td>
      </tr>
      <!--greenline end-->
    ',sizeof($this->columns)+2);
    return $html;
  }

  function listingEnd() {
		foreach ($this->buttons as $button => $url) {
			$html .= sprintf('<a class="button" href="%s">%s</a>',$url,strtoupper($button));
		}
		return sprintf('
  <tr><td colspan="2">&nbsp;</td></tr>
  <tr><td colspan="%d" align="right">%s</td></tr>
  <tr><td colspan="2">&nbsp;</td></tr>
  </table>
  ',sizeof($this->columns)+2,$html);
  }

  function index() {
    return "<a name=top>Index:</a><br />";
	}


  function display($add_index = 0) {
    $html = "";
    if (!sizeof($this->elements))
      return "";
# 	if ($add_index)
#   	$html = $this->index();

    $html .= $this->listingStart();
    $html .= $this->listingHeader();
#    global $float_menu;
#    $float_menu .= "<a style=\"display: block;\" href=\"#".htmlspecialchars($this->title)."\">$this->title</a>";
    foreach ($this->elements as $element) {
      $html .= $this->listingElement($element);
    }
    $html .= $this->listingEnd();
    return $html;
  }
}

class topBar {
	var $type = '';

	function topBar($type) {
  	$this->type = $type;
  }

  function display($lid,$bid) {
  	if ($this->type == "admin") {
    	return $this->adminBar($lid,$bid);
   	} else {
    	return $this->defaultBar();
    }
  }

  function defaultBar() {
  	return '';
  }

  function adminBar($lid,$bid) {
  	global $config;
		return '
<STYLE TYPE="text/css">
   <!--
   a.adminbutton:link {font-family: verdana, sans-serif;font-size : 10px; color : white;background-color : #ff9900; font-weight: normal; border-top: 1px black solid; border-right: 1px black solid; border-left: 1px black solid; text-align : center; text-decoration : none; padding: 2px; width : 80px;}
   a.adminbutton:active {font-family: verdana, sans-serif;font-size : 10px; color : white;background-color : #ff9900; font-weight: normal; border-top: 1px black solid; border-right: 1px black solid; border-left: 1px black solid; text-align : center; text-decoration : none; padding: 2px; width : 80px;}
   a.adminbutton:visited {font-family: verdana, sans-serif;font-size : 10px; color : white;background-color : #ff9900; font-weight: normal; border-top: 1px black solid; border-right: 1px black solid; border-left: 1px black solid; text-align : center; text-decoration : none; padding: 2px; width : 80px;}
	 a.adminbutton:hover {font-family: verdana, sans-serif;font-size : 10px; color : white;background-color : #ff9900; font-weight: normal; border-top: 1px black solid; border-right: 1px black solid; border-left: 1px black solid; text-align : center; text-decoration : none; padding: 2px; width : 80px;}
	 #admineditline {
   	 position:absolute;
		 top:0px; left:0px;
     width:100%;
     background-color:#CCCC99;
     border-style:none;
	   border-bottom: 3px #ff9900 solid;
   }
   -->
</STYLE>
<script language="Javascript" type="text/javascript" src="/codelib/js/cookielib.js"></script>
<script language="Javascript" type="text/javascript">
function hideadminbar() {
  if (document.getElementById) {
		document.getElementById(\'admineditline\').style.visibility="hidden";
	} else {
  	alert("To hide the bar, you need to logout");
  }
}
function closeadminbar() {
  if (document.getElementById) {
		document.getElementById(\'admineditline\').style.visibility="hidden";
    SetCookie("adminbar","hide");
	} else {
  	alert("To hide the bar, you need to logout");
  }
}

</script>

<div id="admineditline">
<!--EDIT TAB TABLE starts-->
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td bgcolor="#CCCC99" height="20" width="60">&nbsp;&nbsp;&nbsp;<a class="adminbutton"
href="'.$config["uploader_dir"]."/?page=edit&b=$bid&id=$lid".'" title="use this link to edit this page">edit page</a></td>
<!--td bgcolor="#CCCC99" height="20" width="70">&nbsp;&nbsp;&nbsp;<a class="adminbutton" href="%s">add images</a></td-->
<!--td bgcolor="#CCCC99" height="20" width="110">&nbsp;&nbsp;&nbsp;<a class="adminbutton" href="%s">change template</a></td-->
<td bgcolor="#CCCC99" height="20" width="70">&nbsp;&nbsp;&nbsp;<a class="adminbutton"
href="'.$config["uploader_dir"]."/?page=logout&return=".urlencode("lid=$lid").'" title="You are logged in as an administrator, click this link to logout">logout</a></td>
<td bgcolor="#CCCC99" height="20">&nbsp;Template: '.getLeafTemplate($lid).'</td>
<td bgcolor="#CCCC99" height="20" width="70"><a href="'.$config["uploader_dir"].'/" class="adminbutton">admin home</a></td>
<td bgcolor="#CCCC99" height="20" width="70">&nbsp;&nbsp;&nbsp;<a class="adminbutton"
href="javascript:hideadminbar();" title="hide the administrative bar on this page">hide bar</a></td>
<td bgcolor="#CCCC99" height="20" width="70">&nbsp;&nbsp;&nbsp;<a class="adminbutton"
href="javascript:closeadminbar();" title="hide the administrative bar permanently">close bar</a></td></tr>
</table>
<!--EDIT TAB TABLE ends-->
</div>
';
	}
}

?>
