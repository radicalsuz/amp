<?php
 
$mod_id=1;
include("sysfiles.php");
#include("includes/base.php");
#include("includes/moduleintro.php");
  //require_once("Connections/functions.php");
$MM_id = $id;
?>	
<html>
<head>
<title><?php echo $SiteName; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="print.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" text="#000000">

<?php $dapath= $base_path_amp."img/printsafelogo.jpg";
if (file_exists("$dapath")) {echo "<img src='img/printsafelogo.jpg'>";}  ?>

<table class="text"><tr><td>

<?php

if ($HTTP_GET_VARS["list"] != "class" ){$isanarticle=1;}
//set default to record one

if (isset($HTTP_GET_VARS["id"]))
  {$calledrcd__MMColParam = $HTTP_GET_VARS["id"];

// find out hierarchy for called record and assign hierarchy vars
$calledrcd=$dbcon->CacheExecute("SELECT articles.author, articles.".$MX_type.", articles.class, articles.id, articletype.parent, articletype.secure, articletype.type as typename FROM articles, articletype  where articletype.id=articles.".$MX_type." and articles.id = " . ($calledrcd__MMColParam) . "") or DIE($dbcon->ErrorMsg());  
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
     
    if ($HTTP_GET_VARS["list"] != NULL) 
                   {include ("list.inc.php");}
				   
			     elseif (($MM_class == 3) or ($MM_class == 4))
				 	{
					 if ($newsreplace != NULL)
				{include("$newsreplace"); }
				else{ include("article.inc.news.php");} 
			 }
					
								
				 elseif ($MM_class == 10)
						{
					 if ($newsreplace != NULL)
				{include("$prreplace"); }
				else{ include("article.inc.pr.php");} 
			 }
					
					
				elseif ($HTTP_GET_VARS["region"] != NULL)
					{ $MM_region = $HTTP_GET_VARS["region"] ;
					include ("list.region.php");}
			     //elseif ($MM_class == 2)
					//{header ("Location: index.php");}
			 else {
			 if ($articlereplace != NULL)
				{include("$articlereplace"); }
				else{ include("article.inc.php");} 
			 }?>
   </td></tr></table>
</body>
</html>
  
