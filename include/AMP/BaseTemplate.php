<?php

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
            $headertitle = ":&nbsp;".$headertitle ;
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
        if ($articleinfo->Fields("linkover")) redirect($articleinfo->Fields("link"));
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

if (!isset($MM_type)) $MM_type = 1;

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
while (!$MM_secure && ($sparent != $MX_top)) {
	$sparent=$obj->get_parent($sparent);
	$getsec=$dbcon->CacheExecute("SELECT secure FROM articletype WHERE id=" . $dbcon->qstr( $sparent )) 
       		or die('Could not load security information in BaseTemplate: ' . $dbcon->ErrorMsg() );
	$MM_secure = $getsec->Fields("secure");
}  

$typetemplate_id = $gettype->Fields("templateid");
$css = $gettype->Fields("css");

#Redirect section
if ($gettype->Fields("uselink")) {redirect($gettype->Fields("linkurl")) ;}

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
$settemplate=$dbcon->CacheExecute("SELECT * FROM template WHERE id = $template_id") or DIE('Could not load template information in BaseTemplate '.$dbcon->ErrorMsg());

$NAV_IMG_PATH = $settemplate->Fields("imgpath");
$NAV_REPEAT = $settemplate->Fields("repeat");
$htmltemplate =$settemplate->Fields("header2");	
if (!$css) {   $css = $settemplate->Fields("css");}
$extra_header = $settemplate->Fields("extra_header");

$lNAV_HTML_1 = $settemplate->Fields("lnav3");		//heading row
$lNAV_HTML_2 = $settemplate->Fields("lnav4");		//close heading row
$lNAV_HTML_3 = $settemplate->Fields("lnav7");		//start content table row
$lNAV_HTML_4 = $settemplate->Fields("lnav8");		//end content table row
$lNAV_HTML_5 = $settemplate->Fields("lnav9");		// content table row spacer
$rNAV_HTML_1 = $settemplate->Fields("rnav3");		//heading row
$rNAV_HTML_2 = $settemplate->Fields("rnav4");		//close heading row
$rNAV_HTML_3 = $settemplate->Fields("rnav7");		//start content table row
$rNAV_HTML_4 = $settemplate->Fields("rnav8");		//end content table row
$rNAV_HTML_5 = $settemplate->Fields("rnav9");		// content table row spacer

#buildheader
$htmlheader= buildheader();


#SECURE THE PAGE

if ($MM_secure) {
	require("AMP/Auth/UserRequire.inc.php");
}

# Start Output Buffering
ob_start();

?>
