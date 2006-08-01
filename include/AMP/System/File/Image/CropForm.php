<?php
require_once( 'AMP/System/Form.inc.php' );
require_once( 'AMP/System/File/Image.inc.php' );

class AMP_System_File_Image_CropForm extends AMPSystem_Form_XML {
    
    var $_crop_values = array(  'start_x', 'start_y', 'end_x', 'end_y', 'height', 'width'),

    function AMP_System_File_Image_CropForm( ){
        $name = 'AMP_System_File_Image_CropForm';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_IMAGES );
    }

    function isSubmitted( ){
        $result = parent::isSubmitted( );
        if ( !$result ) return false;
        require_once( 'AMP/Content/Image/Effects/Controller.php' );
        $effects_controller = &new AMP_Content_Image_Effects_Controller( );
        return $effects_controller->hasImageSizes( 'crop' );
    }


}

?>
