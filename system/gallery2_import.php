<?php
# gallery2 import picture

set_time_limit(0);
require_once( 'AMP/System/Upload.inc.php');
require_once( 'AMP/Content/Image/Resize.inc.php');


function scan_Dir($dir) {

function check_image($img,$base) {
    $image = basename($img ); 
    if (!file_exists ($base.$image)) {
        return $image;
    }
}

$g2_base = AMP_LOCAL_PATH . "/g2data/albums";
$base = 	$dir_original=  AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_URL_IMAGES . "original/";  

$pics = scan_Dir($g2_base);
foreach ($pics as $img) {
    $image = check_image($img,$base);
    print $image;
	#$reSizer = &new ContentImage_Resize();
	#if ( ! ( $reSizer->setImageFile( $img ) && $reSizer->execute() )) {
    #    $result_message = "Resize failed:<BR>". join( "<BR>", $reSizer->getErrors() ) . $result_message ;
    #} 
}

?>