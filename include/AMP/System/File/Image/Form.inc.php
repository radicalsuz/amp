<?php
require_once( 'AMP/System/Form/XML.inc.php');

class AMP_System_File_Image_Form extends AMPSystem_Form_XML {

    var $name_field = 'image';
    
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

    function getIdValue( ){
        if (isset($_REQUEST[ $this->id_field ]) &&  $_REQUEST[ $this->id_field ]) return $_REQUEST[ $this->id_field ];
        return PARENT::getIdValue( );

    }

}

?>
