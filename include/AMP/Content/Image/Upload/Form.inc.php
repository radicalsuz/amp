<?php
require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/Gallery/Image/Form.inc.php');

class AMP_Content_Image_Upload_Form extends AMPSystem_Form_XML {
    var $fieldFile = 'AMP/Content/Image/Upload/Fields.xml';

    var $_gallery_header = array( 
        'gallery_header' => array( 
            'type' => 'header',
            'default' => 'Photo Gallery Settings'));

    function AMP_Content_Image_Upload_Form( ){
        $this->init( 'Image_Upload_Form' );
    }

    function setDynamicValues( ) {
        $this->defineSubmit( 'upload_image', 'Upload Image');
        $this->removeSubmit( 'save');
        $this->removeSubmit( 'cancel');
        $this->addTranslation( 'img', '_getNewImageName', 'get');

    }

    function _getNewImageName( $data, $fieldname ){
        return $data['file_name'];

    }

    function _initUploader( $data, $fieldname, &$upLoader ){
        if ( isset( $data['new_name']) && $data['new_name']) $upLoader->setTargetFileName( $data['new_name']);
        $upLoader->setFolder( AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL );
    }

   
    function _pastSubmitElements( ){
        $galleryForm = &new GalleryImage_Form( );
        $galleryFields = $galleryForm->getFields( );
        unset( $galleryFields['img'] );
        $galleryFields['publish']['attr']['checked'] = 'CHECKED';
        $galleryFields['galleryid']['required'] = false;
        return array_merge( $this->_gallery_header, $galleryFields);
    }
    
    

}

?>
