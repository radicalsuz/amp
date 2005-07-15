<?php
require_once( 'AMP/Form/Deprecated.inc.php' );
require_once( 'AMP/List/Deprecated.inc.php' );

/*
if ( !function_exists( 'autoinc_check' ) ) {
	function autoinc_check ($table,$num) {
		global $dbcon;
		$getid=$dbcon->Execute( "SELECT id FROM $table ORDER BY id DESC LIMIT 1") or die($dbcon->ErrorMsg());
		if ($getid->Fields("id") < $num) { $id = $num; } else { $id = NULL;} 
		return $id;
	}
}
if ( !function_exists( 'helpme2' ) ) {
	function helpme2($link) {
		$output = "<a href=\"javascript:void(0)\" ONCLICK=\"open('help.php?file=$link','miniwin','location=1,scrollbars=1,resizable=1,width=550,height=400')\"><img src=\"images/help.png\" width=\"15\" height=\"15\" border=\"0\" align=\"absmiddle\"></a>&nbsp;";
		return $output;
	}
}	 
if ( !function_exists( 'helpme' ) ) {

	function helpme($link) {
	
		global $PHP_SELF;
		$output="<table width=\"15\" border=\"0\" align=\"right\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><a href=\"javascript:void(0)\" ONCLICK=\"open('help.php?file=";
		
		$pos = strrpos($PHP_SELF, "/");
		$pos = substr($PHP_SELF, ($pos + 1), -4);
		$output.= $pos;
		$output.= "#";
		$output.= $link;
		$output.="','miniwin','location=1,scrollbars=1,resizable=1,width=550,height=400')\"><img src=\"images/help.png\" border=\"0\" align=\"absmiddle\"></a></td></tr></table>";
		return $output;
	
	}
}
*/




function makelistarray($q,$key,$value,$label='Select') {
	global $dbcon;
	$list = array(''=>$label);
	while (!$q->EOF) {
			$list[$q->Fields($key)] =$q->Fields($value);
		$q->MoveNext();
	}
	return $list;
}


function upload_image($newname=NULL,$wwidth,$lwidth,$thumbwidth,$hide_display=NULL){
	global $base_path_amp,$gd_version;

	$picdir = AMP_LOCAL_PATH.DIRECTORY_SEPARATOR."img/original";
	$thumbdir = AMP_LOCAL_PATH.DIRECTORY_SEPARATOR."img/thumb";
	$usedir = AMP_LOCAL_PATH.DIRECTORY_SEPARATOR."img/pic"; 
	$addition = "";
 	$newext = "jpg";

	$array = explode (".",$_FILES['file']['name']);
	$filename = $array[0];
	$extension = strtolower($array[1]);
    if ($_FILES['file']['name'] == "")	{
    } else {
		if(!(($extension == jpe) or ($extension == jpg) or ($extension == jpeg))) {
			$response = "<b>The attached file is not a jpeg!</b>";
        } else {
			if($newname){
				 $filename = $newname; 
			}
            	$smallimage = "$thumbdir"."/"."$filename"."$addition"."."."$newext";
				$useimage = "$usedir"."/"."$filename"."$addition"."."."$newext";
                $original = "$picdir"."/"."$filename"."."."$newext";

			if(file_exists($original)) {
				$response = "<b>A file with this name already exists  on the server</b>";
			} else {
				if (move_uploaded_file($_FILES['file']['tmp_name'], $original)) {  
					$response = "<b>File is valid, and was successfully uploaded.</b>"; 
				} else { 
					$response = "<b>File uploaded failed.</b>";
				}
				if (!copy($original, $useimage)) {
  					echo "<b>failed to copy $useimage...\n</b>";
				}
				if (!copy($original, $smallimage)) {
  					echo "<b>failed to copy $smallimage...\n</b>";
				}
				chmod($smallimage,0755);
				chmod($useimage,0755);
				chmod($original,0755);
				if(file_exists($smallimage)) {
                	$image = imagecreatefromjpeg("$smallimage");
                    $ywert=imagesy($image);
					$xwert=imagesx($image);
					if($xwert > $ywert){
						$verh = $xwert / $ywert;
						$newwidth = $thumbwidth;
						$newheight = $newwidth / $verh;
					} else  {
						$verh = $ywert / $xwert;
                        $newwidth = $thumbwidth;
                        $newheight= $newwidth * $verh;
                   	}
					if ($gd_version >= 2.0) {
            			$destimage = ImageCreateTrueColor($newwidth,$newheight);
                        ImageCopyResampled($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); 
					} else {
						$destimage = ImageCreate($newwidth,$newheight);
                        ImageCopyResized($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); 
					}
                    imagejpeg($destimage,$smallimage);
				}
				if(file_exists($useimage)) {
					$image = imagecreatefromjpeg("$useimage");
                    $ywert=imagesy($image);
                    $xwert=imagesx($image);
					if($xwert > $ywert) {
						$verh = $xwert / $ywert;
                        $newwidth = $wwidth;
                        $newheight = $newwidth / $verh;
					} else  {
                        $verh = $ywert / $xwert;
                        $newwidth = $lwidth;
                        $newheight= $newwidth * $verh;
               		}
					if ($gd_version >= 2.0) {
           				$destimage = ImageCreateTrueColor($newwidth,$newheight);
                    	ImageCopyResampled($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); 
					} else {
           				$destimage = ImageCreate($newwidth,$newheight);
                        ImageCopyResized($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); 
					}
                    imagejpeg($destimage,$useimage);
				}
  			}
		}
	}
	
	if (isset($original)) {
		$response .= '<hr><table>';
        $response .= '<tr><td>Thumbnail:<td><td>'.$smallimage.'</td><td><img src="../img/thumb/'.$filename.$addition.".".$newext."\"></td></tr>";
		$response .= '<tr><td>Optimized:<td><td>'.$useimage.'</td><td><img src="../img/pic/'.$filename.$addition.".".$newext."\"></td></tr>";
		$response .= '<tr><td>Original:<td><td>'.$original.'</td><td><img src="../img/original/'.$filename.$addition.".".$newext."\"></td></tr>";
		$response .= '</table><hr><br>';
	}
	if (!$hide_display) {
		echo $response;
	}
	$image =$filename.$addition.".".$newext;
	return $image;
}


?>
