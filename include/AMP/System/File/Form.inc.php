<?php
require_once( 'AMP/System/Form/XML.inc.php');

class AMP_System_File_Form extends AMPSystem_Form_XML {

    var $name_field = 'file_upload';

    function AMP_System_File_Form( ){
        $name = 'AMP_System_File_Form';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_DOCUMENTS );
    }

    function _initUploader( $data, $filefield, &$upLoader ){
        if ( isset( $data['filename']) && $data['filename']){
            $upLoader->setTargetFileName( $data['filename']);
        }
    }

}

?>