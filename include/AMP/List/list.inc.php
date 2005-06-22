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

//function that creates the sectional header text
function list_header_article($type,$header_url=NULL){
	global $dbcon, $articlereplace, $NAV_IMG_PATH;
	if ($header_url && $header_url != 1 ) {
		$MM_id = $header_url;
	}
	elseif ( $header_url != 1) {
		$sectionheader=$dbcon->CacheExecute("SELECT id  FROM articles WHERE type = $type and class= 8 and publish =1 limit 1")or DIE("Could not load section header info in list".$dbcon->ErrorMsg()); 
		if  ($sectionheader->Fields("id")) {
			$MM_id = $sectionheader->Fields("id");
		}
		else {return false;}
	}
	if (!(isset($_GET['nointro']) && $_GET["nointro"] == 1) && $MM_id) { 
		if ($articlereplace !=NULL) {
			include ("$articlereplace"); 
		}
		else {
		//echo $MM_id;
			include ("AMP/Article/article.inc.php");
		}
		echo '<br>';
		
	}
	if ($MM_id) {return true;}

}

//function that creates the sectional description and title
function list_header_intro($list_name=NULL,$description=NULL,$date=NULL) {
	echo "<p class=title>".$list_name."</p>" ;
	if ($_GET["nointro"] == NULL) {
		if ($description != NULL && $description) { 
			echo "<p class=text>".converttext($description).'</p>'; 
		}
		if  ($date != "00-00-0000" && isset($date)) { 
			echo "<p class=text>".DoDate( $date, 'F j, Y')."<br>".'</p>'; 
		}
	}
}


#get list definiations
if ($_GET["list"] == "type"){
//get list type and repeat info
    $listtype_sql = "SELECT articletype.listtype, articletype.up, listtype.file
                                      FROM articletype, listtype
                                      WHERE articletype.listtype = listtype.id AND articletype.id=$MM_type";
	$listtypeck=$dbcon->CacheExecute( $listtype_sql )
                        or DIE('Could not load section information in list '.$dbcon->ErrorMsg());
//set repeat number
 	if ($listtypeck->Fields("up") != NULL) {
		$mm_limit = $listtypeck->Fields("up");
	}
	$listtype = $listtypeck->Fields("listtype");
//check to see if defualt list is set from url override
	if (isset($_GET['nointro']) && $_GET["nointro"] == 1  &&  $listtype != 4) {
		$listtype = 1;
	}
}

#set the header text query
$section=$dbcon->CacheExecute("SELECT *  FROM articletype WHERE id=$MM_type")or DIE($dbcon->ErrorMsg()); 
$usevar='usetype';
$tvar = "type";

if ($_GET["list"] == "classt" or $_GET["list"] == "class") { 	
	$section=$dbcon->CacheExecute("SELECT *, 1 as header  FROM class  WHERE id = $MM_class") or DIE($dbcon->ErrorMsg());  
	$usevar='useclass';
	$tvar = "class";
}

//set vars needed for display
if (!isset($ttype)) $ttype = '';
$list_name = $section->Fields($tvar).$ttype;
$limit = $section->Fields("up");

if ($section->Fields($usevar) == ("1")){
	$skiplist=1;   
}

##OUT PUT SECTION HADER#####

//check if section is redirected
if ($section->Fields("uselink") == ("1")){
	amp_redirect($section->Fields("linkurl"));
}  


echo '<div id="content_header">';
	
if ( $section->Fields("header")==1 && !(isset($_GET['nointro']) && $_GET["nointro"] == 1)  ){
	if (list_header_article($MM_type,$section->Fields("url")) != true) {
		list_header_intro($list_name,$section->Fields("description"),$section->Fields("date2"));
	}
}
echo '</div>';


##OUT PUT THE LIST ##				

//skip list
if (isset($skiplist)) {

}
else{  
//show search bar
	if ($section->Fields("searchbar") == 1 ){ 
		include("AMP/List/list.search.php"); 
	}

//set list layout
    $listfolder= "AMP/List/";
	if ($listtype != 1 && $listtypeck) {
 		$listfile = $listtypeck->Fields("file");
        if (file_exists_incpath($listfile)) include ("$listfile");
        elseif (file_exists_incpath($listfolder.$listfile)) include ($listfolder.$listfile);
	}
	else {
		include ($listfolder."list.defualt.php");
	}
}
?>
