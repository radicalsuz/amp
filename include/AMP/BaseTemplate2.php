<?php
require_once('AMP/Content/Map.inc.php');

// an attempt to reduce the number of global variables
class AMP_Template {

	var $_nav_positions = array('left' => 'l', 'right' => 'r');

	//replaces the global $lNAV_HTML_<integer> and $rNAV_HTML_<integer>
	var $_nav_html = array();
	//replaces global $NAV_IMG_PATH
	var $_nav_image_path;
	//replaces global $NAV_REPEAT
	var $_nav_repeat;
	//replaces global $htmltemplate
	var $_html_template;
	//replaces global $css
	var $_css;
	//replaces global $extra_header
	var $_extra_header;

	function AMP_Template($id) {
		global $dbcon;
		$templateRecord = $dbcon->CacheExecute("SELECT * FROM template WHERE id = $id") or DIE('Could not load template information in BaseTemplate '.$dbcon->ErrorMsg());

		foreach ($this->_nav_positions as $position => $prefix) {
			$this->_nav_html[$position] = 
				array( 'start_heading' => $templateRecord->Fields($prefix."nav3"),
					   'close_heading' => $templateRecord->Fields($prefix."nav4"),
					   'start_content' => $templateRecord->Fields($prefix."nav7"),
					   'close_content' => $templateRecord->Fields($prefix."nav8"),
					   'content_spacer' => $templateRecord->Fields($prefix."nav9")
					 );
		}
		$this->_nav_image_path = $templateRecord->Fields("imgpath");
		$this->_nav_repeat = $templateRecord->Fields("repeat");
		$this->_html_template = $templateRecord->Fields("header2");
		$this->_css = $templateRecord->Fields("css");
		$this->_extra_header = $templateRecord->Fields("extra_header");
	}

	function getNavHtml($position, $element) {
		return $this->_nav_html[$position][$element];
	}

	function getNavImagePath() {
		return $this->_nav_image_path;
	}

	function getNavRepeat() {
		return $this->_nav_repeat;
	}

	function getHtmlTemplate() {
		return $this->_html_template;
	}

	function getCSS() {
		return $this->_css;
	}

	function getExtraHeader() {
		return $this->_extra_header;
	}
}

if ( !function_exists( 'buildheader' ) ) {
		
    function buildheader() {
        
        global $AmpPath, $MM_title, $MM_shortdesc, $MM_id, $systemplate_id, $meta_description, $meta_content, $mod_name, $SiteName, $Web_url, $extra_header, $css, $SystemSettings, $MM_typename ;

        $encoding = (isset($SystemSettings['encoding'])) ? $SystemSettings['encoding'] : 'iso-8859-1'; 

        if (!isset($htmlheader)) $htmlheader = "";

        $htmlheader .= "<html>
        <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=$encoding\">";
        
        //build header title
        if ($MM_id) {
            $headertitle = $MM_title;
            $meta_description = substr(trim($MM_shortdesc),0,250); 
            $meta_description = ereg_replace ("\"", "", $meta_description);
        } elseif (isset($_GET['list']) && $_GET["list"] == "type") {
            if (isset($MM_typename)) {
                $headertitle = $MM_typename;
            } else {
                $headertitle = "";
            }
        } else {
            $headertitle = $mod_name;
        }

        if (!isset($mod_id) || $mod_id != 2) {
            $headertitle = "&nbsp;&nbsp;".$headertitle ;
        } else {
            $headertitle = "";
        }

        if ($headertitle =="Article") $headertitle = "";
        
        $htmlheader.="<meta http-equiv=\"Description\" content=\"$meta_description\">\n" .
                     "<meta name=\"Keywords\" content=\"$meta_content\">\n" .
                     "<link rel=\"Search\" href=\"/search.php\">\n";
        if ( file_exists( $AmpPath . "img/favicon.ico" ) ) {
            $htmlheader .= '<link rel="icon" href="' . $AmpPath . 'img/favicon.ico" type="image/x-icon" />';
        }

        $htmlheader.="<title>".$SiteName.$headertitle."</title>\n";

        $allsheets=explode(", ", $css);

        for ($i=0;  $i<count($allsheets);$i++) {
                $htmlheader.="<link href=\"".$Web_url.trim($allsheets[$i])."\" rel=\"stylesheet\" type=\"text/css\">\n";
        }
		if ($extra_header) {
			$htmlheader.=$extra_header;
		}
        $htmlheader.="<script language=\"JavaScript\" src=\"".$Web_url."scripts/functions.js\"></script>\n";
		$htmlheader.= "</head>\n";
        return $htmlheader;

    }
}

if (!isset($intro_id) || !$intro_id) { $intro_id = $mod_id; }
if (!isset($intro_id) || !$intro_id) { $intro_id = 1; }
	
#ESTABLISH HIERARCHY
if ($intro_id == 1) {
    
    #GET ARTICLE VARS
    if (isset($_GET['id']) && $_GET["id"]) {
        $articleinfo=$dbcon->CacheExecute("SELECT author, title, type, class, link, linkover FROM articles WHERE id=".$_GET["id"]) or die('Could not load article information in BaseTemplate '.$dbcon->ErrorMsg()); 
        $MM_id = $_GET["id"];
        $MM_class =$articleinfo->Fields("class");
        $MM_type =$articleinfo->Fields("type");
        $MM_author = $articleinfo->Fields("author");
        $MM_title = $articleinfo->Fields("title");
        $MM_shortdesc = $articleinfo->Fields("shortdesc");
        if ($articleinfo->Fields("linkover")) ampredirect($articleinfo->Fields("link"));
    }
    
    if (isset($_GET["type"])  && $_GET['type'] ) $MM_type  = $_GET["type"];
    if (isset($_GET['class']) && $_GET["class"]) $MM_class = $_GET["class"];

} else {

	#GET MODULE TEXT VARS
	$sql = "SELECT templateid, title, name, type FROM moduletext WHERE id = $intro_id";
	$getmodhierarchy=$dbcon->CacheExecute($sql) 
        or DIE('Could not load module hierarchy information in BaseTemplate '.$sql.$dbcon->ErrorMsg());

	$MM_type = $getmodhierarchy->Fields("type");
	$mod_name = $getmodhierarchy->Fields("title");
	$MM_title = $getmodhierarchy->Fields("title");
	$modtemplate_id = $getmodhierarchy->Fields("templateid");
 
} 

if (!isset($MM_type)||!$MM_type) $MM_type = 1;

# GET HIERARCHY VARS
$gettype=$dbcon->CacheExecute("select type, parent, templateid, css, secure, uselink, linkurl from articletype where id = $MM_type")
    or DIE('Could not load sectional heierarcy information in BaseTemplate '.$dbcon->ErrorMsg()); 

$MM_typename = $gettype->Fields("type");
$MM_parent = $gettype->Fields("parent");
$MM_secure = $gettype->Fields("secure");
// work up hierarchy to ensure page is not protected
$sparent= $MM_type;
if ($MX_top != NULL) {$MX_top ='1';}
if (!isset($obj)) { 
    require_once('AMP/Article/menu.class.php');
    $obj = & new Menu; 
}
$content_map = &AMPContent_Map::instance();
$content_map->init( $dbcon, $MX_top );
while (!$MM_secure && ($sparent != $MX_top)) {
	$sparent=$obj->get_parent($sparent);
	$getsec=$dbcon->CacheExecute("SELECT secure FROM articletype WHERE id=" . $dbcon->qstr( $sparent )) 
       		or die('Could not load security information in BaseTemplate: ' . $dbcon->ErrorMsg() );
	$MM_secure = $getsec->Fields("secure");
}  

$typetemplate_id = $gettype->Fields("templateid");
$css = $gettype->Fields("css");

#Redirect section
if ($gettype->Fields("uselink")) {ampredirect($gettype->Fields("linkurl")) ;}

#SET MODULE SPECIFIC VARS
if (isset($modid)) {
    $modinstance = $dbcon->CacheExecute("SELECT * from module_control where modid = $modid") or DIE('Could not load module vars in BaseTemplate '.$dbcon->ErrorMsg());
    while (!$modinstance->EOF) {
    $a = $modinstance->Fields("var");
    $$a = $modinstance->Fields("setting");
    $modinstance->MoveNext();} 
}

#SET TEMPLATE VARS
#DETERMINE TEMPLATE ID
if (isset($modtemplate_id)) {
	$template_id = $modtemplate_id;
} elseif ($typetemplate_id) {
	$template_id = $typetemplate_id;
} elseif ( (!isset($template_id) || !$template_id) && $MM_type != 1 ) { 

	$tparent= $MM_type;
 
	if (isset($css_inherit)) { //Search for template and css files to inherit
 
		while ((!$template_id || !$css) && ($tparent != $MX_top)) {
			$tparent=$obj->get_parent($tparent);
			$gettemplate=$dbcon->CacheExecute("SELECT templateid, css FROM articletype WHERE id = $tparent") or DIE('Could not load template information in BaseTemplate ');
			if (!$template_id) $template_id = $gettemplate->Fields("templateid");
			if (!$css) $css=$gettemplate->Fields("css");
		}  
	} else { //Search for template only
		while (!(isset($template_id) && $template_id) && ($tparent != $MX_top)) {
			$tparent=$obj->get_parent($tparent);
			$gettemplate=$dbcon->CacheExecute("SELECT templateid FROM articletype WHERE id = $tparent") or DIE('Could not load template information in BaseTemplate ');
			$template_id = $gettemplate->Fields("templateid");
		}  
	}
}

if (!isset($template_id) || !$template_id) {$template_id = $systemplate_id;}

#SET TEMPLATE VARS
//$settemplate=$dbcon->CacheExecute("SELECT * FROM template WHERE id = $template_id") or DIE('Could not load template information in BaseTemplate '.$dbcon->ErrorMsg());
$registry =& AMP_Registry::instance();

$template =& new AMP_Template($template_id);
$registry->setTemplate($template);

$NAV_IMG_PATH = $template->getNavImagePath();
$NAV_REPEAT = $template->getNavRepeat();
$htmltemplate = $template->getHtmlTemplate();
if (!$css) {
	$css = $template->getCSS();
}
$extra_header = $template->getExtraHeader();

$lNAV_HTML_1 = $template->getNavHtml('left', 'start_heading');
$lNAV_HTML_2 = $template->getNavHtml('left', 'close_heading');
$lNAV_HTML_3 = $template->getNavHtml('left', 'start_content');
$lNAV_HTML_4 = $template->getNavHtml('left', 'close_content');
$lNAV_HTML_5 = $template->getNavHtml('left', 'content_spacer');
$rNAV_HTML_1 = $template->getNavHtml('right', 'start_heading');
$rNAV_HTML_2 = $template->getNavHtml('right', 'close_heading');
$rNAV_HTML_3 = $template->getNavHtml('right', 'start_content');
$rNAV_HTML_4 = $template->getNavHtml('right', 'close_content');
$rNAV_HTML_5 = $template->getNavHtml('right', 'content_spacer');

#buildheader
$htmlheader= buildheader();


#SECURE THE PAGE

if ($MM_secure) {
	require("AMP/Auth/UserRequire.inc.php");
}

# Start Output Buffering
ob_start();

?>
