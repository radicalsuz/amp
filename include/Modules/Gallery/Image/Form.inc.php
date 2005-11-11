<?php

require_once( 'AMP/System/Form/XML.inc.php');

class GalleryImage_Form extends AMPSystem_Form_XML {

    function GalleryImage_Form( ){
        $name = "galleryImages";
        $this->init( $name, 'POST', 'gallery_image.php?action=list' );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'date', '_makeDbDateTime', 'get');
    }

}

?>
