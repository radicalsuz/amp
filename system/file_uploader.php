<?php
require_once( 'AMP/BaseDB.php');
require_once('tesUpload/html/upload_helper.php');
?>
<html>
<head>
  <title>File Upload</title>
  <link rel="stylesheet" type="text/css" href="/scripts/tesUpload/html/upload.css">
  <script language="javascript" type="text/javascript" src="/scripts/tesUpload/html/prototype.js"></script>
  <script language="javascript" type="text/javascript" src="/scripts/tesUpload/html/upload.js"></script>
</head>
<?php
    $uploaded_file = false;
    if(isset($_POST['userfile']) && ($sid = $_POST['userfile']) 
      && !empty($sid)) {
        require_once('tesUpload/html/read_settings.php');
        require_once('tesUpload/html/receive_helper.php');
        $file = tes_receive($sid);
        $uploaded_file = process_uploaded_file($file, "$tes_upload_dir/$file", true);
    }

?>
<body>
<form name="postform" method="post" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" enctype="multipart/form-data">
<input type="hidden" name="doctype" value="<?php echo (isset($_REQUEST['doctype'])?$_REQUEST['doctype']:'img'); ?>" />
<?php 
    echo tes_upload_value('userfile') ;
?>
1) Click this button and select the file to upload from your computer:<br><br>
</form>

<?php
  tes_upload_form('userfile', '');
  print '<BR />' . $uploaded_file . '<BR />';
?>
</body>
</html>
<?php

function process_uploaded_file($file_name, $file_path, $allow_existing_file=false) {
    include("AMP/System/Upload.inc.php");
    include("AMP/Content/Image/Resize.inc.php");

    $upLoader = &new AMPSystem_Upload( $file_name);
    $image_path = AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL ; 

    $folder_okay = true ;
    if ($_REQUEST['doctype']=='img' && (!$upLoader->setFolder( $image_path ))) {
        $folder_okay=false;
    }
    if ($folder_okay && $upLoader->execute( $file_path, $allow_existing_file ) ) { 
        $new_file_name = basename( $upLoader->getTargetPath() ) ;
        $result_message = "<BR><font face='arial' size=2>File was successfully uploaded.<br>".
            "<br><b>Filename:</b>". $new_file_name ."</font>"; //"</a></font></b><br><br><br><br><br><br><br><br>\n"
            //.return_filename($new_file_name);
        
        if ($_REQUEST['doctype']=='img') {
            $reSizer = &new ContentImage_Resize();
            require_once( 'AMP/Content/Image.inc.php');
            if ( ! ( $reSizer->setImageFile( $upLoader->getTargetPath() ) && $reSizer->execute() )) {
                $result_message = "Resize failed:<BR>". join( "<BR>", $reSizer->getErrors() ) . $result_message ;
            } else {
                $imageRef = &new Content_Image( $file_name );
                $result_message = '<image src="'. $imageRef->getURL( AMP_IMAGE_CLASS_THUMB ) .  '" align="left" border="0">' . $result_message;
            }
        }
    } else {
            $result_message =  "File Upload Failed<BR>\n" . join( '<BR>', $upLoader->getErrors() );
    }
    return $result_message;
}

?>
