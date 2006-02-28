<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/Gallery/Image/ComponentMap.inc.php');

class GalleryImage_Form extends AMPSystem_Form_XML {

    function GalleryImage_Form( ){
        $name = "galleryImages";
        $this->init( $name );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'date', '_makeDbDateTime', 'get');
    }

}

?>
