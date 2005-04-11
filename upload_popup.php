<?php
#ob_start();
set_time_limit(0);
include("AMP/BaseDB.php");
include("AMP/Image/Upload.inc.php");

function return_filename($filename) {
$pass_script="
<script type=\"text/javascript\">

//this javascript function
//puts the file name back on the parent form
function passback ( ) {
		var userfile='".$filename."';
		var parentform='".$_GET['pform']."';
		var calledfield='".$_GET['pfield']."';
		window.opener.document.forms[parentform].elements[calledfield].value = userfile ;
		window.self.close();
		
}
passback();
</script>

";
return $pass_script;

}

function find_safe_filename($filename, $num=0){
	$ext_spot = strrpos($filename, "." );
	if ($num==0) {
		$test_filename=$filename;

	} else {

		$test_filename=substr($filename, 0, $ext_spot)."_".$num.substr($filename, $ext_spot);
	}
	$num++;
	if (file_exists($test_filename)) {
		return find_safe_filename($filename, $num);
	} else {
		return $test_filename;
	}
}


if ($_POST['Submit']){
    $img = new Image_Upload ( $_FILES['userfile']['name'] );
    if ( $img->extension ) {
        $img->makethumb();
        $img->makepic();
        $uploadfile=$img->imgpaths['original'].$img->name.".".$img->extension;
        $uploadfile=find_safe_filename($uploadfile);
        $new_file_name = basename($uploadfile);
        $img->name=$new_file_name;
        if ($img->saveImagesAMP()) {
            echo "<font face='arial' size=2>File was successfully uploaded.<br>
                <br><b>Close this window and enter this exact filename into the box on the form:</b>
                <br><br> <b><a href=\"#\" onclick=\"passback();\"><font color=003399>".
                $new_file_name."</font></a></font></b><br><br><br><br><br><br><br><br>\n"
                .return_filename($new_file_name);
        } else {
            print $img->error;
        }
    } else {
       
        $uploaddir = AMP_LOCAL_PATH.'/downloads/';
        $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
        $uploadfile=find_safe_filename($uploadfile);
        $new_file_name=basename($uploadfile);
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            echo "<font face='arial' size=2>File was successfully uploaded.<br>
                <br><b>Close this window and enter this exact filename into the box on the form:</b>
                <br><br> <b><a href=\"#\" onclick=\"passback();\"><font color=003399>".
                $new_file_name."</font></a></font></b><br><br><br><br><br><br><br><br>\n"
                .return_filename($new_file_name);
        } else {
            echo "Possible file upload attack!\n";
        }
    }

}



?>
<html><body>
<font face="arial" size="2">
<b>Upload a file</b><br>
<br>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
1) Click this button and select the file to upload from your computer:<br><br>
<input name="userfile" type="file" ><br><br>
2) Click the "Upload" button to upload the file:<br>
<input type="submit" name="Submit" value="Upload Document"></form>
</font></body></html>
