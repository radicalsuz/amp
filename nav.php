<?php

 $nav=$dbcon->CacheExecute("SELECT * FROM navtbl WHERE id = " .$nav_var. "") or DIE($dbcon->ErrorMsg());
 
 if ($navside == l) {
    $NAV_HTML_1 = $lNAV_HTML_1;
    $NAV_HTML_2 = $lNAV_HTML_2;
    $NAV_HTML_3 = $lNAV_HTML_3;
    $NAV_HTML_4 = $lNAV_HTML_4;
    $NAV_HTML_5 = $lNAV_HTML_5;}
 if ($navside == r) {
    $NAV_HTML_1 = $rNAV_HTML_1;
    $NAV_HTML_2 = $rNAV_HTML_2;
    $NAV_HTML_3 = $rNAV_HTML_3;
    $NAV_HTML_4 = $rNAV_HTML_4;
    $NAV_HTML_5 = $rNAV_HTML_5;}
 
 ##  GET DIFFERNT NAV TEMPLATES ####
if ($settemplatex  or (($nav->Fields("templateid") != $template_id) && ($nav->Fields("templateid") != 0) ) ){
if ($settemplatex ){$template_id2 =  $settemplatex;}
else{$template_id2 = $nav->Fields("templateid");}

 $settemplate=$dbcon->CacheExecute("SELECT * FROM template WHERE id = $template_id2") or DIE($dbcon->ErrorMsg());
if ($navside == l) {
   $NAV_HTML_1 = $settemplate->Fields("lnav3");	//heading row
   $NAV_HTML_2 = $settemplate->Fields("lnav4");	//close heading row
   $NAV_HTML_3 = $settemplate->Fields("lnav7");		//start content table row
   $NAV_HTML_4 = $settemplate->Fields("lnav8");		//end content table row
   $NAV_HTML_5 = $settemplate->Fields("lnav9");		// content table row spacer
   }
if ($navside == r) {
   $NAV_HTML_1 = $settemplate->Fields("rnav3");	//heading row
   $NAV_HTML_2 = $settemplate->Fields("rnav4");	//close heading row
   $NAV_HTML_3 = $settemplate->Fields("rnav7");		//start content table row
   $NAV_HTML_4 = $settemplate->Fields("rnav8");		//end content table row
   $NAV_HTML_5 = $settemplate->Fields("rnav9");		// content table row spacer
   }
   }
   
###DEFINE NON SQL NAVIGATION

//TITLE AS IMAGE 
  if ($nav->Fields("nosql") == ('1') &&  $nav->Fields("titletext") != (NULL)  ) { 
  if ($nav->Fields("titleti") == ('1')) { //start image
$shownav.="<img src=\"".$NAV_IMG_PATH ; 
	 $shownav.= $nav->Fields("titleimg")."\">";
} 
//TITLE AS TEXT	
	if ($nav->Fields("titleti") != ('1')) { 
	 $shownav.= $NAV_HTML_1 ; 
	$shownav.= nl2br($nav->Fields("titletext")); 
	$shownav.=$NAV_HTML_2 ; 
	}
//BODY
	$shownav.= $NAV_HTML_3 ;
		$nonsqlreturn = $nav->Fields("nosqlcode");

	$shownav.= evalhtml($nonsqlreturn);
	$shownav.= $NAV_HTML_4 ; 
	$shownav.= $NAV_HTML_5 ;
}

  elseif ($nav->Fields("nosql") == ('1') &&  $nav->Fields("titletext") == (NULL)) { //start nonsql
    $shownav.= $nav->Fields("nosqlcode") ;}

###DEFINE SQL GENERATED NAVIGATION
else {
//deal with database php
$sqlx = $nav->Fields("sql") ;
$sqlx = addslashes($sqlx) ;
eval("\$sqlx = \"$sqlx\";");
$sqlx = stripslashes($sqlx);
$limit = $nav->Fields("repeat");
$alimit = $limit + 1;
if ($limit !=700) {
$sqlx = $sqlx." Limit ".$alimit; }
 $nested=$dbcon->CacheExecute($sqlx) or DIE($dbcon->ErrorMsg());
   $nested_numRows=0;
   $nested__totalRows=$nested->RecordCount();

   $Repeat2__numRows = $nav->Fields("repeat");
   $Repeat2__index= 0;
   $nested_numRows = $nested_numRows + $Repeat2__numRows;

if ($nested->Fields("id") == (NULL)) { $rowx_count++;}	//start navigation
else {
##### TITLE ROW ###########
 if ($nav->Fields("titleti") == ('1')) { //start image
 	$shownav.= $NAV_HTML_1;
	$shownav.="<img src=\"".$NAV_IMG_PATH .$nav->Fields("titleimg")."\">";
	$shownav.= $NAV_HTML_2;} //end image 

 if ($nav->Fields("titleti") != ('1')) { //start title text
 	$shownav.= $NAV_HTML_1 ; 

$title = $nav->Fields("titletext") ;
if (ereg('^z{3}',$title ))
{$title = ereg_replace ("zzz", "", $title);
$title = $nested->Fields($title) ;
	$shownav.= $title;
	$shownav.= $NAV_HTML_2 ; }
else {
	$shownav.= $title ;
	$shownav.= $NAV_HTML_2 ; }
 } //end title text 
##### REPEATING MIDDLE CONTENT###########
 while (($Repeat2__numRows-- != 0) && (!$nested->EOF)) 
   { 
	$shownav.=$NAV_HTML_3 ; //start link text
	$shownav.= "<a href=\"";
	$shownav.= $nav->Fields("linkfile") ;
	$shownav.= "?";
	   if ($nav->Fields("mvar1")) { $shownav.=$nav->Fields("mvar1");}
	   else {$shownav.= "id"; }
	   $shownav.="=";
	   if ($nav->Fields("mvar1val")) {   $shownav.= $nested->Fields($nav->Fields("mvar1val"));}
	   else { $shownav.= $nested->Fields("id");}   
	$shownav.="\"";   
    $shownav.=" class=\"";
	    if ($settemplatex ==$temp1 && $settemplatex ){$shownav.= "sidelist2";} elseif ($nav->Fields("linkextra")) {$shownav.= $nav->Fields("linkextra");} else {$shownav.=$sidelistcss; }
    $shownav.="\">";
	    if ($nav->Fields("linkfield")) {	   $shownav.= $nested->Fields($nav->Fields("linkfield"));}
       else {$shownav.= $nested->Fields("title");}
	  $shownav.="</a>";
      $shownav.= $NAV_HTML_4 ; 
	  
	  
  $Repeat2__index++;
  $nested->MoveNext();
} 
######### REPEAT ROW ###############
 if (($nested->RecordCount()) > ($nav->Fields("repeat"))) { 				//start more
      $shownav.= $NAV_HTML_3 ; 									
      $shownav.="<A HREF=\"";
	  $shownav.= $nav->Fields("mfile")."?list=".$nav->Fields("mcall1");
	       if  ($nav->Fields("mcall1") == "classt") {$shownav.= "&type=$MM_type" ; }
	  $shownav.="&".$nav->Fields("mvar2")."=".$nested->Fields($nav->Fields("mcall2"))."\" class=\"go\">More&nbsp;&#187;</a>";
      $shownav.= $NAV_HTML_4 ;  } 													//end more
  
       $shownav.=$NAV_HTML_5 ; 	//end navigation
  $nested->Close();
 } 
 }
  ?>
 