<?php

include("AMP/BaseDB.php");
$pic_path = AMP_LOCAL_PATH."/img/";

$max_fsize = 102400; // Maximum filesize

if (isset($file)){
  $source = $file;
  $source_name = $file_name;
  if(($source <> "none")&&($source <> "")){   //Checkes wether the uploaded file exists
  $dest = $pic_path.$source_name;
    if(move_uploaded_file($source,$dest)){
      $transfer=$pic_path.$source_name;
      $size = GetImageSize($transfer);
      $fsize = filesize($transfer);
      $img_width=$size[0];
      $img_height=$size[1];

      $ext = substr($source_name, strrpos($source_name, ".")+1);  //Strips the extension of the image
      switch (strtoupper($ext)){
        case "JPEG":
          $format = "jpg";
          $aut="1";
          break;
        case "JPG":
          $format = "jpg";
          $aut="1";
          break;
      }

      if ($aut == "1")  {    //If the filetype is correct the authorisation is set to 1
        if ($fsize > $max_fsize) {
          $foutje=$pic_path."$source_name";
          @unlink($foutje);
          echo "The picture you sent had a filesize of $fsize bytes. This is too big! Mazimum filesyze is $max_fsize bytes.";
          exit;
        }

        $random_name = md5(uniqid(rand()));
        $session=$random_name.".";
      $new_file=$pic_path.'crop_'.$file_name;  
		#$new_file=$pic_path.$session.$format;
        #$new_img = $session.$format;

 		$new_img = 'crop_'.$file_name;

       $upit = rename($transfer,$new_file);
		if ($upit == FALSE){
			rename($transfer,$pic_path.$session.$format);
			$new_file=$pic_path.$session.$format;
		}
        @unlink($source);
      }
      else {
        echo"Your file does not have the right filetype. Only Jpeg files are supported.";
        @unlink($transfer);
        exit;
      }
    }
    else {
      echo "Somethig went wrong try again!\n";      // you need to write-enable the upload directory
      exit;
    }
  } else {
    echo "no valid file sent!";
    exit;
  }

  if ($keep_logs == 1){
    write_log($pic_log);
  }
} else {
  echo "no valid file sent!";
  exit;
}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Step 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 9px; color: #330000}
a {  font-weight: bold; color: #CC0000; text-decoration: none}
a:hover {  text-decoration: underline}
-->
</style>
<script language="JavaScript" type="text/JavaScript">
<!--
function checkSize(){
  var wVar = document.step2form.widthVal.value;
  var hVar = document.step2form.heightVal.value;
  if ((hVar < <?=($img_height-5)?> && hVar > 0) && (wVar < <?=($img_width-5)?> && wVar > 0)){
      var url = 'crop_image.php?org=<?=$new_file?>&crh='+hVar+'&crw='+wVar
      location.href = url;
  } else {
      alert ('crop size has to be between <?=($img_width-5)?> and <?=($img_height-5)?>.');
  }
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="75%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle"><strong>Step 2</strong>:<br>
      <br><IMG SRC="crop_image.php?org=<?=$new_file?>&crA=img"><br><Br>
      Select the size to which you want to crop:<br>
      <form name="step2form" method="post" action="">
        width: 
        <input name="widthVal" type="text" size="3" maxlength="3">
        &nbsp;&nbsp;&nbsp;height: 
        <input name="heightVal" type="text" size="3" maxlength="3">
        &nbsp;&nbsp;&nbsp;
        <input name="Button" type="button" onClick="checkSize();" value="Submit">
      </form></td>
  </tr>
</table>
</body>
</html>
