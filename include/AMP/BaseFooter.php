<?php
 /*********************
06-11-2003  v3.01
Module:  Template
Description:  display footer and right nav of template. called from all display pages
To Do: 

*********************/ 
if ($modulefooter != NULL){
	echo $modulefooter;
	$modulefooter=NULL;
}

$bodydata = ob_get_clean();
	$bodydata =$bodydata2.$bodydata;
//get left nav
 //$navside="l";
// $sidelistcss="sidelist";
// include("navselect.php"); 
// $leftnav =  $shownav;

 //get right nav;

 $sidelistcss="sidelist";
// $shownav=NULL;
  include("includes/navselect.php"); 
$navside="l";
$leftnav  = getthenavs($navside);

$navside= "r";
$rightnav  = getthenavs($navside);


$htmltemplate = evalhtml($htmltemplate);
 $htmltemplate=str_replace("[-right nav-]", $rightnav, $htmltemplate);
 $htmltemplate=str_replace("[-left nav-]", $leftnav, $htmltemplate);
 $htmltemplate=str_replace("[-body-]", $bodydata, $htmltemplate);

$htmloutput = $htmlheader.$htmltemplate;
echo $htmloutput;
//echo $leftnav;
 ob_end_flush();
?>







