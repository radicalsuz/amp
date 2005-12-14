<?php
# gallery2 import picture

set_time_limit(0);
require_once( 'AMP/System/Upload.inc.php');
require_once( 'AMP/Content/Image/Resize.inc.php');


function scan_Dir($dir) {   $arrfiles = array();   if (is_dir($dir)) {       if ($handle = opendir($dir)) {           chdir($dir);           while (false !== ($file = readdir($handle))) {               if ($file != "." && $file != ".." && $file !=".DS_Store") {                   if (is_dir($file)) {                       $arr = scan_Dir($file);                       foreach ($arr as $value) {                           $arrfiles[] = $dir."/".$value;                       }                   } else {                       $arrfiles[] = $dir."/".$file;                   }               }           }           chdir("../");       }       closedir($handle);   }   return $arrfiles;}

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