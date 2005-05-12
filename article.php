<?php
/*********************
12-30-2003  v3.01
Module:  Content
Description:  main page for displaying all content and sectional  index pages
To Do: 

*********************/ 
//set modle id

$mod_id = 1 ; 

#redirect pages 
if (isset($_GET['filelink']) && $_GET["filelink"]) {
        header("Location: ".$_GET["filelink"]);
        exit;
}
ob_start();

include("AMP/BaseDB.php");

if (isset($_GET['list']) && $_GET['list'] == "type") {
	$title=$dbcon->CacheExecute("SELECT uselink, linkurl  FROM articletype WHERE id=".$_GET['type'])or DIE($dbcon->ErrorMsg()); 
	if ($title->Fields("uselink") == ("1")){
		$MM_editRedirectUrl = $title->Fields("linkurl");
		header ("Location: $MM_editRedirectUrl");
   }  
}
	
if (isset($_GET['list']) && $_GET["list"] != "class" ){
	$isanarticle=1;
}

if (isset($_GET["id"]))  {
	$calledrcd__MMColParam = $_GET["id"];
// find out hierarchy for called record and assign hierarchy vars
	$calledrcd=$dbcon->CacheExecute("SELECT articles.author, articles.".$MX_type.", articles.class, articles.id, articletype.parent, articletype.secure, articletype.type as typename FROM articles, articletype  where articletype.id=articles.".$MX_type." and articles.id = " . ($calledrcd__MMColParam) . "") or header("Location: search.php"); 
	$MM_id = $calledrcd->Fields("id");
	$MM_type = $calledrcd->Fields("type");
	$MM_parent = $calledrcd->Fields("parent");
	$MM_typename = $calledrcd->Fields("typename");
	$MM_class = $calledrcd->Fields("class");
	$MM_author = $calledrcd->Fields("author");
	$MM_secure = $calledrcd->Fields("secure");
}
	
//Assign hierarchy vars for lists 
	//for type
if (isset($_GET["type"])) {
	$MM_type = $_GET["type"];
	$calledsection=$dbcon->CacheExecute("SELECT secure, type, parent FROM articletype WHERE id = $MM_type") or DIE($dbcon->ErrorMsg());  
	$MM_parent = $calledsection->Fields("parent");
	$MM_typename = $calledsection->Fields("type");
	$MM_secure = $calledsection->Fields("secure");
}
//for class
if (isset($_GET["class"])) {
	$MM_class = $_GET["class"];	 
}
 
if (isset($_GET['list']) && $_GET['list'] == "class" ){
	$class_type=$dbcon->CacheExecute("SELECT  type FROM class WHERE id = $MM_class") or DIE($dbcon->ErrorMsg()); 
	if ($class_type->Fields("type")) {
		$MM_type = $class_type->Fields("type");
 	}
} 
 
//load the template  
//start the page
include("AMP/BaseTemplate.php");

ob_start(); 


//set article or list inc
if (isset($_GET['list']) && $_GET["list"] != NULL) {
	if (isset($listreplace) && $listreplace != null) {include("$listreplace");}
	else  { include ("AMP/List/list.inc.php");}
} 
elseif (($MM_class == 3) or ($MM_class == 4)) {
	if ($newsreplace != NULL) {
		include("$newsreplace"); 
	}
	else	{
		include("AMP/Article/article.inc.news.php");
	} 
}
elseif ($MM_class == 10){
	if (isset($prreplace) && $prreplace != NULL) {
		include("$prreplace"); 
	} else{
        include("AMP/Article/article.inc.pr.php");
    }
}
elseif ($_GET["region"] != NULL){ 
	$MM_region = $_GET["region"] ;
	if ($regionreplace != NULL) {
		include("$regionreplace"); 
	}
	else { 
		include ("AMP/List/list.region.php");
	} 
}
else {
	if ($articlereplace != NULL) {
		include("$articlereplace"); 
	}
	else { 
		include("AMP/Article/article.inc.php");
	} 
}
include("AMP/BaseFooter.php");

?>
