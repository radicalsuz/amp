<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////                                                //////////////////////////
//////////////////////////                 1 2 CROP IMAGE                 //////////////////////////
//////////////////////////                                                //////////////////////////
//////////////////////////             (c) 2002 Roel Meurders             //////////////////////////
//////////////////////////         mail: scripts@roelmeurders.com         //////////////////////////
//////////////////////////                  version 0.2                   //////////////////////////
//////////////////////////                                                //////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
///// CREDITS: Most Javascript is taken from DHTMLCentral.com and is made by Thomas Brattli. ///////
////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////
// SET VARIABLES ///////////////////////////////////////////////////////////////////////////////////

require_once( 'AMP/Content/Image.inc.php' );

$dir_crop = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR .  "img/crop/";
$dir_thumb = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR .  "img/thumb/";

$org = $_GET['org'];
$file_name  = substr(strrchr($_GET['org'], '/'), 1 ) ;//str_replace($pic_path, $pic_path."crop_", $org);
$res = $dir_crop.$file_name;
if (!$_GET['crw']) {
	$crw = AMP_IMAGE_WIDTH_THUMB ;
} else 	{
	$crw = $_GET['crw'];
}
if (!$_GET['crh']) {
	$crh = AMP_IMAGE_WIDTH_THUMB ;
} else 	{
	$crh = $_GET['crh'];
}


  $maxwinw = 2000; //Sets the maxmimum width for displaying the original while cropping
  $maxwinh = 2000; //Same, only this time it's the height

  $jpegqual = 80; //Sets the jpeg quality
  $redirect = "crop_image.php"; //Sets the file to which the forms should be posted Change you include this file in another.
  $javafile = "/scripts/12cropimage.js"; //Relative path to js-file
  $gdversion = 2; // set to 2 if you have gd2 installed
  $spacer = "images/spacer.gif"; //Relactive path tpo spacer.gif, transparent image

  $txt['cropimage'] = "crop";
  $txt['preview'] = "preview";
  $txt['bigger'] = "+";
  $txt['smaller'] = "-";
  $txt['closewindow'] = "close window";
  $txt['selectioninpicture'] = "The selection has to be completely on the image.";


////////////////////////////////////////////////////////////////////////////////////////////////////
// GET PROPORTIONS /////////////////////////////////////////////////////////////////////////////////

  if ($crA != "nada"){
     $size = getimagesize($org);
     $trueW=$size[0];
     $trueH=$size[1];

     if($trueH<$maxwinh && $trueW<$maxwinw){
	  $imgH = $trueH;
	  $imgW = $trueW;
	  $imgProp = 1;
     } else {
	  if (($maxwinh/$maxwinw) < ($trueH/$trueW)){
	     $imgH = $maxwinh;
	     $imgW = ($maxwinh / $trueH) * $trueW;
	     $imgProp = $trueH / $imgH;
	  } else {
	     $imgW = $maxwinw;
	     $imgH = ($maxwinw / $trueW) * $trueH;
	     $imgProp = $trueW / $imgW;
	  }
     }
  }


////////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION TO SHOW THE USER INTERFACE /////////////////////////////////////////////////////////////

  function CR_showUI(){
     global $txt, $imgW, $imgH, $imgProp, $spacer, $redirect, $javafile, $crw, $crh, $org, $res;
?>
<html>
<head>
  <title>Crop image</title>
  <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
  <script language="JavaScript" src="<?=$javafile?>"></script>
  <script language="JavaScript">
    <!--
	function libinit(){
	   obj=new lib_obj('cropDiv')
	   obj.dragdrop()
	}

	function cropCheck(crA){
	   if ((((obj.x + obj.cr)-11) <= <?=$imgW?>)&&(((obj.y + obj.cb)-11) <= <?=$imgH?>)&&(obj.x >= 11)&&(obj.y >= 11)){
		var url = '<?=$redirect?>?&crA='+crA+'&org=<?=$org?>&res=<?=$res?>&crw=<?=$crw?>&crh=<?=$crh?>&l='+(obj.x-11)+'&t='+(obj.y-11)+'&s='+obj.cr;
		if (crA == 'pre'){
		   window.open(url,'prevWin','width=<?=$crw?>,height=<?=$crh?>');
		} else {
		   location.href=url;
		}
	   } else {
		alert('<?=$txt['selectioninpicture']?>');
	   }
	}

	function stopZoom() {
	   loop = false;
	   clearTimeout(zoomtimer);
	}

	function cropZoom(dir) {
	   loop = true;
	   prop = <?=$crh?> / <?=$crw?>;
	   zoomtimer = null;
	   direction = dir;
	   if (loop == true) {
		if (direction == "in") {
		   if ((obj.cr > <?=($crw/$imgProp)?>)&&(obj.cb > <?=($crh/$imgProp)?>)){
			cW = obj.cr - 1;
			cH = parseInt(prop * cW);
			obj.clipTo(0,cW,cH,0,1);
		   }
		} else {
		   if ((obj.cr < (<?=$imgW?>-5))&&(obj.cb < (<?=$imgH?>-5))){
			cW = obj.cr + 1;
			cH = parseInt(prop * cW);
			obj.clipTo(0,cW,cH,0,1);
		   }
		}
		zoomtimer = setTimeout("cropZoom(direction)", 10);
	   }
	}

	onload=libinit;
    // -->
  </script>
  <style>
    body{font-family:arial,helvetica; font-size:12px}
    #cropDiv{position:absolute; left:11px; top:11px; width:<?=($crw/$imgProp)?>px; height:<?=($crh/$imgProp)?>px; z-index:2; background-image: url(<?=$spacer?>); }
  </style>
</head>
<body bgcolor="#FFFFFF" text="#000066" link="#000066" alink="#000066" vlink="#000066" marginwidth="10" marginheight="10" topmargin="10" leftmargin="10">
  <table cellspacing="0" cellpadding="0" border="0">
    <tr>
	<td align="left" valign="top"><IMG SRC="<?=$redirect?>?crA=img&org=<?=$org?>&res=<?=$res?>&crw=<?=$crw?>&crh=<?=$crh?>" border="1"></td>
    </tr>
    <tr>
	<td><img src="<?=$spacer?>" border=0 height="5" width="5"></td>
    </tr>
    <tr>
	<td align="right">
	  <input type="button" name="Submit1" value="<?=$txt['cropimage']?>" onclick="cropCheck('def');">
	  <input type="button" name="Submit2" value="<?=$txt['preview']?>" onclick="cropCheck('pre');">
	  <input type="button" name="Submit3" value="<?=$txt['smaller']?>" onMouseDown="cropZoom('in');" onMouseUp="stopZoom();">
	  <input type="button" name="Submit4" value="<?=$txt['bigger']?>" onMouseDown="cropZoom('out');" onMouseUp="stopZoom();">
	</td>
    </tr>
  </table>
  <div id="cropDiv">
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
	<tr>
	  <td><img src="<?=$spacer?>"></td>
	</tr>
    </table>
  </div>
</body>
</html>
<?
  }


////////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION TO SHOW THE CROP PREVIEW ///////////////////////////////////////////////////////////////

  function CR_showPrev($t,$l,$s){
     global $txt, $imgW, $imgH, $redirect, $crh, $crw, $org, $res;
?>
<html>
<head>
  <title>crop voorbeeld</title>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="top.focus();">
  <a href="#" onClick="top.close();"><img alt="<?=$txt['closewindow']?>" src="<?=$redirect?>?crA=crop&org=<?=$org?>&res=<?=$res?>&crw=<?=$crw?>&crh=<?=$crh?>&l=<?=$l?>&t=<?=$t?>&s=<?=$s?>" border=0></a>
</body>
</html>
<?
  }


////////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION TO ACTUALLY MAKE THE CROP //////////////////////////////////////////////////////////////

  function CR_make_crop($l,$t,$s,$w,$h){
     global $org,$imgProp, $gdversion;
     $l1 = ceil($imgProp * $l);
     $t1 = ceil($imgProp * $t);
     $s1 = ceil($imgProp * $s);
     $s2 = ceil(($h / $w)* $s1);
     if ($gdversion == 2){
	  $new = imagecreatetruecolor($w,$h);
     } else {
	  $new = imagecreate($w,$h);
     }
     $img = imagecreatefromjpeg ($org);
     imagecopyresized ($new, $img, 0, 0, $l1, $t1, $w, $h, $s1, $s2);
     imagedestroy($img);
     return $new;
  }


////////////////////////////////////////////////////////////////////////////////////////////////////
// SCRIPT CONTROL VIA VARIABLE $crA ////////////////////////////////////////////////////////////////

  switch($crA){
     case img:
	  header("Content-Type: image/jpeg");
	  $im = CR_make_crop(0,0,($trueW/$imgProp),$imgW,$imgH);
	  imagejpeg($im,"",$jpegqual);
	  imagedestroy($im);
	  exit;
     case crop:
	  header("Content-Type: image/jpeg");
	  $im = CR_make_crop($l,$t,$s,$crw,$crh);
	  imagejpeg($im,"",$jpegqual);
	  imagedestroy($im);
	  exit;
     case pre:
	  CR_showPrev($t,$l,$s);
	  exit;
     case def:
	  $im = CR_make_crop($l,$t,$s,$crw,$crh);
	  if (!file_exists($dir_crop)) { mkdir($dir_crop, 0755) ;}
	  $fh=fopen($res,'w');
	  fclose($fh);
	  imagejpeg($im,$res,$jpegqual);
	  unlink($dir_thumb.$file_name);
	  
	$timage_p = imagecreatetruecolor(AMP_IMAGE_WIDTH_THUMB, AMP_IMAGE_WIDTH_THUMB);
  	$timage = imagecreatefromjpeg($res);
	imagecopyresampled($timage_p, $timage, 0, 0, 0, 0, AMP_IMAGE_WIDTH_THUMB, AMP_IMAGE_WIDTH_THUMB, $crw,$crh);
	imagejpeg($timage_p, $dir_thumb.$file_name, $jpegqual);

	 ?><a href="#" onClick="top.close();"><img alt="<?=$txt['closewindow']?>" src="<?=$redirect?>?crA=img&org=<?=$res?>" border=0><br>Close Window</a>
<?php	  
imagedestroy($im);
	  break;
     default:
	  CR_showUI();
	  exit;
  }

?>
