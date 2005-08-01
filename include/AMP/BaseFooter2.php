<?php

/* * * * * * * * * *
 * AMP Base Footer
 */

require_once ('AMP/Content/Page.inc.php' );

$currentPage = AMPContent_Page::instance();
if (isset($modulefooter) && $modulefooter){
    $currentPage->addtoContentFooter( $modulefooter );
}

if (!isset($bodydata2)) $bodydata2 = "";
$currentPage->setContent( ob_get_clean().$bodydata2 );


if  (isset($_GET['printsafe']) && $_GET['printsafe'] == 1) {
    print $currentPage->output( AMP_CONTENT_PAGE_DISPLAY_PRINTSAFE );
} else {
    print $currentPage->output();
}

@ob_end_flush();
/*
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

} else {
    echo $htmloutput;
}
*/


?>
