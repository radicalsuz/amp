<?php
require_once( 'AMP/System/File/Form.inc.php');

class AMP_System_File_Image_Form extends AMP_System_File_Form {
    
    function AMP_System_File_Image_Form( ){
        $name = 'AMP_System_File_Image_Form';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_IMAGES );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'date', '_makeDbDateTime');
    }

    function _initUploader( $data, $filefield, &$upLoader ){
        $upLoader->setFolder( 'img/original');
        if ( isset( $data['filename']) && $data['filename']){
            $upLoader->setTargetFileName( $data['filename']);
        }
    }

}

?>
