<?php
/*********************
12-30-2003  v3.01
Module:  Content
Description:  main page for displaying all content and sectional  index pages
To Do: 

*********************/ 
//set modle id

$mod_id = 1 ; 
if ($_GET["filelink"]) {
        header("Location:".$_GET["filelink"]);
        die();
}
ob_start();
//load cahce functions
 require_once("adodb/adodb.inc.php");
require_once("Connections/freedomrising.php");
if ($_GET['list'] == "type") {

 $title=$dbcon->CacheExecute("SELECT uselink, linkurl  FROM articletype WHERE id=$type")or DIE($dbcon->ErrorMsg()); 
 if ($title->Fields("uselink") == ("1")){
   $MM_editRedirectUrl = $title->Fields("linkurl");
  // echo $MM_editRedirectUrl;
   header ("Location: $MM_editRedirectUrl");
   }  
    }
	
//load sysfiles

include("Connections/menu.class.php");
$obj = new Menu;

if ($HTTP_GET_VARS["list"] != "class" ){$isanarticle=1;}
//set default to record one

if (isset($HTTP_GET_VARS["id"]))
  {$calledrcd__MMColParam = $HTTP_GET_VARS["id"];

// find out hierarchy for called record and assign hierarchy vars
$calledrcd=$dbcon->CacheExecute("SELECT articles.author, articles.".$MX_type.", articles.class, articles.id, articletype.parent, articletype.secure, articletype.type as typename FROM articles, articletype  where articletype.id=articles.".$MX_type." and articles.id = " . ($calledrcd__MMColParam) . "") or header("Location: search.php");// DIE($dbcon->ErrorMsg());  
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
	if (isset($HTTP_GET_VARS["type"]))
 	 {$MM_type = $HTTP_GET_VARS["type"];
	 $calledsection=$dbcon->CacheExecute("SELECT secure, type, parent FROM articletype WHERE id = $MM_type") or DIE($dbcon->ErrorMsg());  
	 $MM_parent = $calledsection->Fields("parent");
	$MM_typename = $calledsection->Fields("type");
	$MM_secure = $calledsection->Fields("secure");
	
	 }
	 //for class
	 	if (isset($HTTP_GET_VARS["class"]))
 	 {$MM_class = $HTTP_GET_VARS["class"];	 }

   
//load the template  
require_once("Connections/templateassign.php");  

//start the page
 
 include("headerdata.php"); 
 ob_start(); 
 //secure the page
 if ($MM_secure) {require("password/secure.php");

$valper=$dbcon->Execute("SELECT perid FROM permission WHERE groupid = $userLevel and perid= 43") or DIE($dbcon->ErrorMsg());
if (!$valper->Fields("perid")) {
	session_start();
	session_unregister("login");
	session_unregister("password");
	session_destroy();
	$sessionPath = session_get_cookie_params(); 
	setcookie(session_name(), "", 0, $sessionPath["path"], $sessionPath["domain"]); 
	$redire = $PHP_SELF. "?" . $QUERY_STRING."&fail=1";
  header ("Location: $redire");
;}
}

//printersafe page
 
  if ($HTTP_GET_VARS["print"] == "1")  { 
  echo $headerdata;
 if ($HTTP_GET_VARS["list"] != (NULL)) 
                  {include ("list.inc.php");}
			     elseif (($MM_class == 3) or ($MM_class == 4))
					{include ("article.inc.news.php");}
			     elseif ($MM_class == 10)
					{include ("article.inc.pr.php");}
			     //elseif ($MM_class == 2)
					//{header ("Location: index.php");}
			 else {
			 include("article.inc.php");
			}
			 echo "</body></html>";
			 ob_end_flush();}
 
 else {
 

                    
//set article or list inc
		
			     if ($HTTP_GET_VARS["list"] != NULL) 
                   {if ($listreplace != NULL) {include("$listreplace");}
					 else  { include ("list.inc.php");}
				   } 
			     elseif (($MM_class == 3) or ($MM_class == 4))
				 	{
					 if ($newsreplace != NULL)
				{include("$newsreplace"); }
				else{ include("article.inc.news.php");} 
			 }
					
								
				 elseif ($MM_class == 10)
						{
					 if ($prreplace != NULL)
				{include("$prreplace"); }
				else{ include("article.inc.pr.php");} 
			 }
					
					
				elseif ($HTTP_GET_VARS["region"] != NULL)
					{ $MM_region = $HTTP_GET_VARS["region"] ;
					if ($regionreplace != NULL)
						{include("$regionreplace"); }
						else{ include ("list.region.php");} 
					}
			     //elseif ($MM_class == 2)
					//{header ("Location: index.php");}
			 else {
			 if ($articlereplace != NULL)
				{include("$articlereplace"); }
				else{ include("article.inc.php");} 
			 }
									 	
include ("footer.php");
}//endfooter
?>
