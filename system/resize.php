<?php
if ($_GET['action']) {
	
	set_time_limit(0);
	require_once( 'AMP/System/Upload.inc.php');
	require_once( 'AMP/Content/Image/Resize.inc.php');
	
	
	$dir_thumb= AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_URL_IMAGES . "thumb/";
	$dir_pic= AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_URL_IMAGES . "pic/";
	
	$dir_original=  AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_URL_IMAGES . "original";  
	$dir_crop=  AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_URL_IMAGES . "crop";  
	
	$dir = opendir($dir_original);
	
	while ($file_name = readdir($dir)) {
		if (($file_name !=".") && ($file_name != "..")) {
			unlink($dir_thumb.$file_name);
			unlink($dir_pic.$file_name);
			$img = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_URL_IMAGES . "original/".$file_name;
			$reSizer = &new ContentImage_Resize();
			if ( ! ( $reSizer->setImageFile( $img ) && $reSizer->execute() )) {
				$result_message = "Resize failed:<BR>". join( "<BR>", $reSizer->getErrors() ) . $result_message ;
			} 
		}
	}
	
	$dir = opendir($dir_crop);
	while ($file_name = readdir($dir)) {
		if (($file_name !=".") && ($file_name != "..")) {
			unlink($dir_thumb.$file_name);
			$img = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_URL_IMAGES . "crop/".$file_name;
	
			$timage_p = imagecreatetruecolor(AMP_IMAGE_WIDTH_THUMB, AMP_IMAGE_WIDTH_THUMB);
			$timage = imagecreatefromjpeg($img);
			imagecopyresampled($timage_p, $timage, 0, 0, 0, 0, AMP_IMAGE_WIDTH_THUMB, AMP_IMAGE_WIDTH_THUMB, AMP_IMAGE_WIDTH_THUMB,AMP_IMAGE_WIDTH_THUMB);
			imagejpeg($timage_p, $dir_thumb.$file_name,'80');
	
		}
	}
}

ampredirect("/system/imgdir.php");

?>
