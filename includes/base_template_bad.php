<?php
#ESTABLISH HIERARCHY
if ($mod_id == 1) {
    
    #GET ARTICLE VARS
    if ($_GET[id]) {
         $articleinfo=$dbcon->CacheExecute("SELECT author, title, type, class, link, linkover  FROM articles WHERE id=$_GET[id]")or DIE($dbcon->ErrorMsg()); 
        $MM_id = $_GET[id];
        $MM_class =$articleinfo->Fields("class");
        $MM_type =$articleinfo->Fields("type");
        $MM_author = $articleinfo->Fields("author");
        $MM_title = $articleinfo->Fields("title");
        $MM_shortdesc = $articleinfo->Fields("shortdesc");
        if ($articleinfo->Fields("linkover")) redirect($articleinfo->Fields("link"));
    }
    
    if ($_GET[type]) $MM_type = $_GET[type];
    if ($_GET["class"])	$MM_class = $_GET["class"];

} else {

	#GET MODULE TEXT VARS

	$getmodhierarchy=$dbcon->CacheExecute("SELECT templateid, title, name, type FROM moduletext WHERE id = $mod_id") or DIE($dbcon->ErrorMsg());

	$MM_type = $getmodhierarchy->Fields("type");
	$mod_name = $getmodhierarchy->Fields("name");
	$MM_title = $getmodhierarchy->Fields("title");
	$modtemplate_id = $getmodhierarchy->Fields("templateid");
 
} 

if (!$MM_type) $MM_type = 1;

# GET HIERARCHY VARS
$gettype=$dbcon->CacheExecute("select type, parent, templateid, css, secure, uselink, linkurl from articletype where id = $MM_type")or DIE($dbcon->ErrorMsg()); 

$MM_typename = $gettype->Fields("type");
$MM_parent = $gettype->Fields("parent");
$MM_secure = $gettype->Fields("secure");
$typetemplate_id = $gettype->Fields("templateid");
$css = $gettype->Fields("css");

#Redirect section
if ($gettype->Fields("uselink")) {redirect($gettype->Fields("linkurl")) ;}

#SET MODULE SPECIFIC VARS
if (isset($modid)) {
    $modinstance = $dbcon->CacheExecute("SELECT * from module_control where modid = $modid") or DIE($dbcon->ErrorMsg());
    while (!$modinstance->EOF) {
    $a = $modinstance->Fields("var");
    $$a = $modinstance->Fields("setting");
    $modinstance->MoveNext();} 
}

#SET TEMPLATE VARS

#DETERMIN TEMPLATE ID
if ($modtemplate_id) {
	$template_id = $modtemplate_id;
} elseif ($typetemplate_id) {
	$template_id = $typetemplate_id;
} elseif ( (!$template_id) && $MM_type != 1 ) { 

	$tparent= $MM_type;
 
	while (!$template_id && ($tparent != $MX_top)) {
		$tparent=$obj->get_parent($tparent);
		$gettemplate=$dbcon->CacheExecute("SELECT templateid FROM articletype WHERE id = $tparent") or DIE("dd");
		$template_id = $gettemplate->Fields("templateid");
	}  
}

if (!$template_id)  {$template_id = $systemplate_id;}

#SET TEMPLATE VARS
$settemplate=$dbcon->CacheExecute("SELECT * FROM template WHERE id = $template_id") or DIE($dbcon->ErrorMsg());

$NAV_IMG_PATH = $settemplate->Fields("imgpath");
$NAV_REPEAT = $settemplate->Fields("repeat");
$htmltemplate =$settemplate->Fields("header2");	
if (!$css) {   $css = $settemplate->Fields("css");}

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

    require($base_path."password/secure.php");
    
    // needs to make this better
    $valper=$dbcon->CacheExecute("SELECT perid FROM permission WHERE groupid = $userLevel and perid= 43") or DIE($dbcon->ErrorMsg());
    if (!$valper->Fields("perid")) {
        session_start();
        session_unregister("login");
        session_unregister("password");
        session_destroy();
        $sessionPath = session_get_cookie_params(); 
        setcookie(session_name(), "", 0, $sessionPath["path"], $sessionPath["domain"]); 
        $redire = $PHP_SELF. "?" . $_SERVER['QUERY_STRING']."&fail=1"; 
        header ("Location: $redire");
	}
}

# Start Output Buffering
ob_start();

?>
