<?php
#defual list layout

#set list defualts
$sqlorder = " Order by pageorder asc, date desc, id desc ";
$sqlorder2= " Order by date desc, id desc ";
if (!$limit) {$limit=20;}

//set up defulat list class types
$classselect = " (class !=2 && class !=8 && class !=9) and ";
if ($MM_classselect ) { 
	$classselect = $MM_classselect .' and ';
}

#set the main query
if ($_GET['type']) {
	$wtype = " and type=$MM_type ";
	if  ($MM_reltype){
		$wtype = "and (type=$MM_type or typeid = $MM_type) ";
		$joinreltype = "  Left Join articlereltype  on articleid = id ";
	}
}

if ($_GET['class']) {
	$wclass =  " and class=$MM_class ";
	$classselect ='';
	$_GET["nointro"] =1;
}

if ($_GET['author']) {
	$wauthor = " and author= '".$_GET['author']."' ";  
	$classselect ='';
}

if ($_GET['area']) {
	$warea = " and region= '".$_GET['area']."' ";  
	$classselect ='';
}

if ($_GET['year']) {
	$wyear =   " and YEAR(date) as date = $_GET[year] ";
	$classselect ='';
}

$sql = $joinreltype . ' WHERE  ' . $classselect . ' publish =1  ' . $wreltype . $warea . $wclass . $wauthor . $wyear .$wtype . $sqlorder;
//echo $sql;

$sqlct  = "SELECT  COUNT(DISTINCT id)  from articles".$sql;
$listct=$dbcon->CacheExecute("$sqlct")or DIE("could not get list count".$dbcon->ErrorMsg());
$sqlsel = "SELECT  DISTINCTROW  id, link, linkover, shortdesc, date, usedate, author,  source, sourceurl, picuse, picture, title FROM articles";

if (isset($_GET['offset'])) {
	$soffset = $_GET['offset'];
} 
else {
	$soffset = 0;
}
$sqloffset = " LIMIT $soffset,$limit ";
if (isset($_GET['all'])) {
	$sqloffset ="";
}

$sql = $sqlsel.$sql.$sqloffset;
$list=$dbcon->CacheExecute("$sql")or DIE("Could not build list:<br>".$sql.'<br>'.$dbcon->ErrorMsg());

// calll the layout file
if ($listlayoutreplace !=NULL) {
	include("$listlayoutreplace"); 
}	
else { 
	include ("AMP/List/list.layout.inc.php"); 
}

// show pagination
if ($limit < $listct->fields[0]) {
	echo '<br><br><div align="right">';
	$MM_removeList = "&offset=, &all=,&nointro=";
	reset ($_GET);
	while (list ($key, $val) = each ($_GET)) {
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
	
	if ( $soffset  != 0 && $all !=1 ) { 
		echo '&nbsp; <a href="' . $MM_moveFirst . '" >&laquo;&nbsp;First Page</a>'; 
	} 
	if ( $soffset  != 0 && $all !=1 ) { 
		echo '&nbsp; <a href="' . $MM_moveFirst . '" >&laquo;&nbsp;</a><a href="' . $MM_movePrev . '" >Previous Page </a>';
	} 
	if ( $soffset  != $loffset  && $all !=1) { 
		echo '&nbsp;&nbsp; <a href="' . $MM_moveNext . '" >Next Page &raquo;</a>'; 
	} 
	if ( $soffset  != $loffset && $all !=1 ) { 
		echo '&nbsp;&nbsp; <a href="' . $MM_moveLast . '" >Last Page &raquo;</a> ';
	}
	if($all !=1 ) { 
		echo '&nbsp;&nbsp; <a href="' . $PHP_SELF."?".$MM_keepURL . '&all=1&nointro=1">All Articles&raquo;</a>'; 
	}
	echo '</div>';
}
?>