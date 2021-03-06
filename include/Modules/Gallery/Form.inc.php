<?php
require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/System/ComponentMap.inc.php');

class Gallery_Form extends AMPSystem_Form_XML {
    var $name_field = "galleryname";

    function Gallery_Form( ){
        $name = 'gallery';
        $this->init( $name );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'date', '_makeDbDateTime', 'get');
        $this->addTranslation( 'id', '_showGalleryImages', 'set');
    }

    function _showGalleryImages( $data, $fieldname ) {
        if ( isset( $data['id'])) {
            $this->setFieldValueSet( 'img', AMPContentLookup_GalleryImages::instance( $data['id']));
            return $data['id'];
        }
    }

    function _selectAddNull( $valueset, $name ) {
        if( $name != 'parent') return parent::_selectAddNull( $valueset, $name );
        $current_gallery_id = ( isset( $_REQUEST['id']) && $_REQUEST['id']) ? $_REQUEST['id'] : false;
        if( $current_gallery_id ) unset( $valueset[$current_gallery_id]);
        return parent::_selectAddNull( $valueset, $name );
    }
}
?>
