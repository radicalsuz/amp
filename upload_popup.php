<?php
#ob_start();
set_time_limit(0);
include("AMP/BaseDB.php");
include("AMP/System/Upload.inc.php");
include("AMP/Content/Image/Resize.inc.php");

function return_filename($filename) {
    if ( !isset( $_GET['pform']) || !isset( $_GET['pfield'])) {
        return false;
    }
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

if(isset($_REQUEST['handler']) && ('tesUpload' == $_REQUEST['handler'])) {
  if(isset($_POST['userfile']) && ($sid = $_POST['userfile']) 
    && !empty($sid)) {
    require_once('tesUpload/html/read_settings.php');
    require_once('tesUpload/html/receive_helper.php');
    $file = tes_receive($sid);
    print process_uploaded_file($file, "$tes_upload_dir/$file", true);
    exit;
  }

  require_once('tesUpload/html/upload_helper.php');
?>
<html>
<head>
  <title>File Upload</title>
  <link rel="stylesheet" type="text/css" href="/scripts/tesUpload/html/upload.css">
  <script language="javascript" type="text/javascript" src="scripts/tesUpload/html/prototype.js"></script>
  <script language="javascript" type="text/javascript" src="scripts/tesUpload/html/upload.js">></script>
</head>
<body>
<form name="postform" method="post" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" enctype="multipart/form-data">
<input type="hidden" name="doctype" value="<?= (isset($_REQUEST['doctype'])?$_REQUEST['doctype']:'img'); ?>" />
<?php echo tes_upload_value('userfile') ?>
1) Click this button and select the file to upload from your computer:<br><br>
</form>

<?php
  tes_upload_form('userfile', '');
?>
</body>
</html>
<?php

} else {
    if ($_POST['Submit'] && isset ($_FILES['userfile']['tmp_name']) && file_exists( $_FILES['userfile']['tmp_name'])){
      print process_uploaded_file($_FILES['userfile']['name'], $_FILES['userfile']['tmp_name']);
      exit;
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
<?php
}

function process_uploaded_file($file_name, $file_path, $allow_existing_file=false) {

    $upLoader = &new AMPSystem_Upload( $file_name);
    $image_path = AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL ; 

    $folder_okay = true ;
    if ($_REQUEST['doctype']=='img' && (!$upLoader->setFolder( $image_path ))) {
        $folder_okay=false;
    }
    if ($folder_okay && $upLoader->execute( $file_path, $allow_existing_file ) ) { 
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
    return $result_message;
}

?>
