<?php
/*********************
12-30-2003  v3.01
Module: Article
Description:  sectional index page
CSS: title, text, go
calls: list.layot.php, pagation.php, var based file
Called By: article.php
GET VARS: 
					$list = type, class, classt, heroi, heror
					$nointro =1 - overrides list to defualt with no introduction
   					$all = 1 - overrise the pagation var and show all articles
To Do:
*********************/ 

#SELECT distinct YEAR(date)  FROM `articles`  where YEAR(date)!='NULL' and publish=1 and usedate !=1 order by date desc
#SELECT id  FROM `articles` where YEAR(date)=2003 order by date desc

$mm_limit =20; //defualt limit for pagaiton if not set
$sqlorder = " Order by pageorder asc, date desc, id desc ";
$sqlorder2= " Order by date desc, id desc ";
################## DEFINE  lLIST FORMAT AND REPEAT NUMBER FROM TYPE DEF  #######################
if ($HTTP_GET_VARS["list"] == "type"){
//get list type and repeat info
 $listtypeck=$dbcon->CacheExecute("SELECT articletype.listtype, articletype.up, listtype.file  FROM articletype, listtype WHERE articletype.listtype = listtype.id and articletype.id=$MM_type")or DIE($dbcon->ErrorMsg());
//set repeat number
 if ($listtypeck->Fields("up") != NULL) {$mm_limit = $listtypeck->Fields("up");}
$listtype = $listtypeck->Fields("listtype");
//check to see if defualt list is set from url override
if ($_GET["nointro"] == 1  &&  $listtype != 4) {$listtype = 1;}
}

//set mannual override to display all article
if (isset($_GET["all"])) { $limit = " 30, 100";}
else {$limit = $mm_limit;}


################## DEFINE  LIST PAGES #######################
 $classselect = "(class !=2 && class !=8 && class !=9)";
 if ($MM_classselect ) { $classselect = $MM_classselect;}
 	$usevar= "usetype";
		$tvar = "type";

 // TYPE LIST (requires ?type)
if ($_GET["list"] == ("type")) {
$sql="  WHERE $MX_type=$MM_type and publish=1 and   $classselect   Order by  pageorder asc, date desc, id desc";
   $section=$dbcon->CacheExecute("SELECT *  FROM articletype WHERE id=$MM_type")or DIE($dbcon->ErrorMsg()); 
   if  ($AMP_view_rel){
   $sql="  WHERE ($MX_type=$MM_type or relsection1 = $MM_type or relsection2 = $MM_type) and publish=1 and   $classselect   $sqlorder";}
     if  ($MM_reltype){
$sql="  Left Join articlereltype  on articleid = id  WHERE (type=$MM_type or typeid = $MM_type) and publish=1 and   $classselect   $sqlorder";
}
	   } 

// CLASS LIST (?class supplied)	   
if ($_GET["list"] == "class") {
 $sql="  WHERE class=$MM_class and  publish=1 $sqlorder2 ";
$section=$dbcon->CacheExecute("SELECT *  FROM class  WHERE id = $MM_class") or DIE($dbcon->ErrorMsg());  
		$usevar= "useclass";
		$tvar = "class";
		 }
		 
		 
		 if ($_GET["list"] == "archive") {
 $sql="  WHERE class=$MM_class and  YEAR(date) = $_GET[year]  and publish=1 $sqlorder2 ";
$section=$dbcon->CacheExecute("SELECT *  FROM class  WHERE id = $MM_class") or DIE($dbcon->ErrorMsg());  
		$usevar= "useclass";
		$tvar = "class";
		 }
		 
//CLASS in a type LIST (?class & type supplied)  this will return all articles in a class
if ($HTTP_GET_VARS["list"] == "classt") { 	 
  $sql=" WHERE class=$MM_class and $MX_type=$MM_type and  publish=1 $sqlorder ";
   if  ($MM_reltype){
$sql="  Left Join articlereltype  on articleid = id  WHERE  class=$MM_class and (type=$MM_type or typeid = $MM_type) and publish=1   $sqlorder";
}
  $section=$dbcon->CacheExecute("SELECT *  FROM class  WHERE id =$MM_class") or DIE($dbcon->ErrorMsg());  
  		$usevar= "useclass";
		$tvar = "class";
		$ttype =":  ".$MM_typename;
		}

	if ($HTTP_GET_VARS["list"] == "authort") { 
$section=$dbcon->CacheExecute("SELECT *  FROM articletype WHERE id=$MM_type") or DIE($dbcon->ErrorMsg());  
  $sql="  WHERE publish=1 and  $classselect  and $MX_type=$MM_type and uselink=1 and author= '".$_GET[author]."'  order by date desc ";
	}
		if ($HTTP_GET_VARS["list"] == "author") { 
$section=$dbcon->CacheExecute("SELECT *  FROM articletype WHERE id=$MM_type") or DIE($dbcon->ErrorMsg());  
  $sql="  WHERE publish=1 and  $classselect  and  uselink=1 and author= '".$_GET[author]."'  order by date desc ";
	}

 // TYPE LIST with all relational content (requires ?type)
if ($_GET["list"] == ("typer")) {
$sql="  WHERE ($MX_type=$MM_type or relsection1 = $MM_type or relsection2 = $MM_type) and publish=1 and   $classselect   $sqlorder";
   $section=$dbcon->CacheExecute("SELECT *  FROM articletype WHERE id=$MM_type")or DIE($dbcon->ErrorMsg()); 
	   } 

	if ($HTTP_GET_VARS["list"] == "rel") { 
$section=$dbcon->CacheExecute("SELECT *  FROM articletype WHERE id=$MM_type") or DIE($dbcon->ErrorMsg());  
  $sql="  WHERE publish=1 and   $classselect  and $MX_type=$MM_type and uselink=1 and relsection2= ".$_GET[rel2]." and relsection1= ".$_GET[rel1]." order by date desc ";
	}
	
				if ($HTTP_GET_VARS["list"] == "rel1") { 
$section=$dbcon->CacheExecute("SELECT *  FROM articletype WHERE id=$MM_type") or DIE($dbcon->ErrorMsg());  
  $sql="  WHERE publish=1 and  $classselect  and $MX_type=$MM_type and uselink=1 and relsection1= ".$_GET[rel1]." order by date desc ";
	}
	
					if ($HTTP_GET_VARS["list"] == "rel2") { 
$section=$dbcon->CacheExecute("SELECT *  FROM articletype WHERE id=$MM_type") or DIE($dbcon->ErrorMsg());  
  $sql="  WHERE publish=1 and   $classselect  and $MX_type=$MM_type and uselink=1 and relsection2= ".$_GET[rel2]." order by date desc ";
	}

################### REDIRECT OR ADD INTRO PAGE ###################		 
  		//check if section is redirected
  			if ($section->Fields("uselink") == ("1")){
   				$MM_editRedirectUrl = $section->Fields("linkurl");
   				header ("Location: $MM_editRedirectUrl");
  				 }  
		//add the section header text
    			if ($section->Fields("header") ){
			if ($section->Fields("url")  &&  $section->Fields("url") != 1 ) {
   				$MM_id = $section->Fields("url");}
				elseif ( $section->Fields("url") != 1) {
				$sectionheader=$dbcon->CacheExecute("SELECT id  FROM articles WHERE $MX_type=$MM_type and class= 8 and publish =1 limit 1")or DIE($dbcon->ErrorMsg()); 
				if  ($sectionheader->Fields("id")) {$MM_id = $sectionheader->Fields("id");}
			}
  				if ($_GET["nointro"] !=1 && $MM_id) { 
				if ($articlereplace !=NULL) {
				//echo "<br>";
				include ("$articlereplace"); 
				echo "<br>";}
				else{
				//echo "<br>"; 
				include ("article.inc.php");
				echo "<br>"; }
     			}
				
				} 
				
	   //check if list should be skiped
	  		if ($section->Fields($usevar) == ("1")){
   				$skiplist=1;   
				}
		// assign title if no article descritption is used
			 else {$list_name = $section->Fields($tvar).$ttype ;}


################# CREATE LIST  HEADER ##################

//skip list
	if (isset($skiplist)){}

else{  
//populate section title and description
?>
<div id="content_header">
<?php if ($section->Fields("url") == ("1") or  !$section->Fields("header") or ($HTTP_GET_VARS["nointro"] ==1)){
echo "<p class=title>".$list_name."</p>" ;
if ($_GET["nointro"] == NULL) {
 if ($section->Fields("description") != NULL) { 
echo "<p class=text>".converttext($section->Fields("description"))."<br>"; }
if  ($section->Fields("date2") != "00-00-0000") { echo DoDate( $section->Fields("date2"), 'F j, Y')."<br>"; }
echo "</p>";
 }
 }?>
 </div>
 <?php
 if ($section->Fields("searchbar") == 1 ){  include("list.search.php"); }
################# INCLUDE NON DEFUAL LIST LAYOUT ##################
 if   ((isset($listtype)) & ($listtype !=1)) {
 		 $listfile = $listtypeck->Fields("file");
			include ("$listfile");}
else {

################### DEFUALT LIST LAYOUT ########################
//set repeat number and set pagation variables
$sqlct  = "SELECT  COUNT(DISTINCT id)  from articles".$sql;
$listct=$dbcon->CacheExecute("$sqlct")or DIE($dbcon->ErrorMsg());
$sqlsel = "SELECT  DISTINCTROW  id, link, linkover, shortdesc, date, usedate, author, source, source, sourceurl, picuse, picture, title FROM articles";

if (isset($_GET[offset])) {$soffset = $_GET[offset];} 
else {$soffset = 0;}
$sqloffset = " LIMIT $soffset,$limit ";
if (isset($_GET[all])) {$sqloffset ="";}

$sql = $sqlsel.$sql.$sqloffset;
$list=$dbcon->CacheExecute("$sql")or DIE($dbcon->ErrorMsg());


   if ($listlayoutreplace !=NULL) {include("$listlayoutreplace"); 
		}	else{include ("list.layout.inc.php"); };?>


<br><br>
<div align="right"> 



  <?php
  if ($limit < $listct->fields[0]) {
  
  $MM_removeList = "&offset=, &all=,&nointro=";
reset ($HTTP_GET_VARS);
while (list ($key, $val) = each ($HTTP_GET_VARS)) {
	$nextItem = "&".strtolower($key)."=";
	if (!stristr($MM_removeList, $nextItem)) {
		$MM_keepURL .= "&".$key."=".urlencode($val);
	}
}
$MM_moveFirst=   $PHP_SELF."?".$MM_keepURL."&offset=0";
$MM_moveNext =  $PHP_SELF."?".$MM_keepURL."&nointro=1&offset=".($soffset+$limit);
$MM_movePrev =  $PHP_SELF."?".$MM_keepURL."&nointro=1&offset=".($soffset-$limit);
$loffset = (floor($listct->fields[0] / $limit) * $limit);
$MM_moveLast =  $PHP_SELF."?".$MM_keepURL."&nointro=1&offset=".($loffset);


  ?>
  <?php if ( $soffset  != 0 && $all !=1 ) { ?>
  &nbsp; <a href="<?php echo $MM_moveFirst?>" class="go"> 
  &laquo;&nbsp;First Page</a> 

  <?php  } if ( $soffset  != 0 && $all !=1 ) { ?>
  &nbsp; <a href="<?php echo $MM_moveFirst?>" class="go">&laquo;&nbsp;</a><a href="<?php echo $MM_movePrev?>" class="go">Previous 
  Page </a> 

 <?php  } if ( $soffset  != $loffset  && $all !=1) { ?>
  &nbsp;&nbsp; <a href="<?php echo $MM_moveNext?>" class="go">Next 
  Page &raquo;</a> 

 <?php  } if ( $soffset  != $loffset && $all !=1 ) { ?>
  &nbsp;&nbsp; <a href="<?php echo $MM_moveLast?>" class="go">Last 
  Page &raquo;</a> 

  <?php 
  } if($all !=1 ) { ?>

  &nbsp;&nbsp; <span class="go"><a href="<?php echo $PHP_SELF."?".$MM_keepURL;?>&all=1&nointro=1">All 
  Articles&raquo;</a></span> 
  <?php 
  }
  }
  ?>
</div> 
<?php 
 
  }//end defulat list
}//end skip list
?>
