<?php
/*********************
07-22-2003  v3.01
Module:  Template Include
Description:  displays breadcrumb on page, added in via the template system
CSS: breadcrumb
To Do: 

*********************/
global $MM_type, $_GET, $MM_class ,$area, $list, $MM_id, $mod_name, $mod_id, $isanarticle, $obj;
//include("system/Connections/menu.class.php");
//if (isset($area)){
//$histate=$dbcon->CacheExecute("Select title from region where id = $area") or DIE($dbcon->ErrorMsg());
//}

if ($_GET["list"]=="class"){
$hiclass=$dbcon->CacheExecute("Select class from class where id = $MM_class") or DIE($dbcon->ErrorMsg());
}
if ($MM_id){
$hiarticle=$dbcon->CacheExecute("Select title from articles where id = $MM_id") or DIE($dbcon->ErrorMsg());
}

if (($mod_id==1) && ($_GET["list"]!="class")){
$hitype=$dbcon->CacheExecute("Select type, id from articletype where id = $MM_type") or DIE($dbcon->ErrorMsg());
}
####strat html #################
 $bchtml.= " <!-- BEGIN BREADCRUMB CODE -->
 <span class=breadcrumb><a href=\"".$Web_url ."index.php\" class=breadcrumb>Home</a> ";

   if (isset($isanarticle)){
   if ($MM_type != 1){
$ancestors = $obj->get_ancestors("$MM_type");
 for ($x=0; $x<sizeof($ancestors); $x++)
{ 
if ($ancestors[$x]["id"] != "1" ){
$path .= "<b>&nbsp;&#187;&nbsp;</b><a href=\"" . $Web_url . "article.php?list=type&type=" . $ancestors[$x]["id"] . "\" class=\"breadcrumb\">" . $ancestors[$x]["type"] . "</a>" . "&nbsp;";
}
} 
$bchtml.= $path; 
}   
 if ($MM_type != "1" && $mod_id=="1"){
$path2 .= "<b>&nbsp;&#187;&nbsp;</b><a href=\"" . $Web_url . "article.php?list=type&type=" . $hitype->Fields("id") . "\" class=\"breadcrumb\">" . $hitype->Fields("type") . "</a>" . "&nbsp;";
$bchtml.= $path2; }

  if (!$_GET["list"] && $MM_id && !$mod_name) { 
	 $maxTextLenght=35;
  $aspace=" ";
  $tttext =$hiarticle->Fields("title");
  if(strlen($tttext) > $maxTextLenght ) {
     $tttext = substr(trim($tttext),0,$maxTextLenght); 
     $tttext = substr($tttext,0,strlen($tttext)-strpos(strrev($tttext),$aspace));
    $tttext = $tttext.'...';
  }
  $bchtml.=  "<span class=breadcrumb></span>&nbsp;&nbsp;<strong>&#187;</strong>&nbsp;&nbsp;".$tttext."</span>"; }
  }
   if (!$_GET["list"] && $mod_name) { $bchtml.= "&nbsp;<strong>&nbsp;&#187;</strong>&nbsp;&nbsp;".$mod_name ;  }
	
   if ($_GET["list"] == "class") {$bchtml.=  "<b>&nbsp;&#187;&nbsp;</b><a href=\"".$Web_url."article.php?list=class&class=".$MM_class."\" class=breadcrumb>".$hiclass->Fields("class")."</a>"; }  
  
 //  if  ($_GET[area]) { $bchtml.= "<b>&nbsp;&#187;&nbsp;</b>".$histate->Fields("title")."</a>"; }  
$bchtml.="</span>";
echo $bchtml;
?>
