<?php
#ob_start();
set_time_limit(0);
include("AMP/BaseDB.php");
include("AMP/System/Upload.inc.php");
include("AMP/Content/Image/Resize.inc.php");

function return_filename($filename) {
$pass_script="
<script type=\"text/javascript\">
//<!--
//this javascript function
//puts the file name back on the parent form
function passback ( userfile, parentform, parentform_element ) {
    window.opener.document.forms[parentform].elements[ parentform_element ].value = userfile ;
    window.self.close();
		
}
passback( '". $filename . "', '".$_GET['pform']."', '".$_GET['pfield']."');
//-->
</script>

";
return $pass_script;

}


if ($_POST['Submit'] && isset ($_FILES['userfile']['tmp_name']) && file_exists( $_FILES['userfile']['tmp_name'])){

    $upLoader = &new AMPSystem_Upload( $_FILES['userfile']['name'] );
    $image_path = AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL ; 

    $folder_okay = true ;
    if ($_REQUEST['doctype']=='img' && (!$upLoader->setFolder( $image_path ))) {
        $folder_okay=false;
    }
    if ($folder_okay && $upLoader->execute( $_FILES['userfile']['tmp_name'] ) ) { 
        $new_file_name = basename( $upLoader->getTargetPath() ) ;
        $result_message = "<font face='arial' size=2>File was successfully uploaded.<br>".
            "<br><b>Filename:</b>". $new_file_name ."</font></a></font></b><br><br><br><br><br><br><br><br>\n"
            .return_filename($new_file_name);
        
        if ($_REQUEST['doctype']=='img') {
            $reSizer = &new ContentImage_Resize();
            if ( ! ( $reSizer->setImageFile( $upLoader->getTargetPath() ) && $reSizer->execute() )) {
                $result_message = "Resize failed:<BR>". join( "<BR>", $reSizer->getErrors() ) . $result_message ;
            }
        }
    } else {
            $result_message =  "File Upload Failed<BR>\n" . join( '<BR>', $upLoader->getErrors() );
    }
    print $result_message;

}



?>
<html><body>
<font face="arial" size="2">
<b>Upload a file</b><br>
<br>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
<input type="hidden" name="doctype" value="<?= (isset($_REQUEST['doctype'])?$_REQUEST['doctype']:'img'); ?>" />
1) Click this button and select the file to upload from your computer:<br><br>
<input name="userfile" type="file" ><br><br>
2) Click the "Upload" button to upload the file:<br>
<input type="submit" name="Submit" value="Upload Document"></form>
</font></body></html>
