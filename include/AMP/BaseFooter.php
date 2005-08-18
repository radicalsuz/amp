<?php
 /*********************
06-11-2003  v3.01
Module:  Template
Description:  display footer and right nav of template. called from all display pages
To Do: 

*********************/ 
if (AMP_USE_NEW_TEMPLATE_ENGINE) {
    require_once( 'AMP/BaseFooter2.php' );
} else {

if (isset($modulefooter) && $modulefooter){
	echo $modulefooter;
	$modulefooter=NULL;
}

$bodydata = ob_get_clean();

if (!isset($bodydata2)) $bodydata2 = '';
$bodydata =$bodydata2.$bodydata;

$sidelistcss="sidelist";

include("AMP/Nav/navselect.php"); 
$navside="l";
$leftnav  = getthenavs($navside);

$navside= "r";
$rightnav  = getthenavs($navside);


$htmltemplate = evalhtml($htmltemplate);
$htmltemplate2 = str_replace("[-right nav-]", $rightnav, $htmltemplate);
$htmltemplate2 = str_replace("[-left nav-]", $leftnav, $htmltemplate2);
$htmltemplate2 = str_replace("[-body-]", $bodydata, $htmltemplate2);

$htmloutput = $htmlheader . $htmltemplate2;

if  (isset($_GET['printsafe']) && $_GET['printsafe'] == 1) {
	$printer_safe_top= "<div class=printer_safe_top></div>";
	echo $htmlheader.$printer_safe_top.$bodydata;
} else {
    echo $htmloutput;
}

@ob_end_flush();
}
?>
