<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Stylesheet/ComponentMap.inc.php');

class AMP_Content_Stylesheet_Form extends AMPSystem_Form_XML {

    function AMP_Content_Stylesheet_Form( ) {
        $name = 'css_edit_form';
        $this->init( $name, 'POST', 'stylesheet.php' );
        $header = & AMP_getHeader( );
        $header->addJavaScript( 'scripts/picker.js', 'color_picker');
    }

    //overrides standard behavior for non-numeric filename ids
    function getIdValue() {
        if ( isset($_REQUEST[ $this->id_field ]) && ( $_REQUEST[ $this->id_field ] )) return $_REQUEST[ $this->id_field ];
        return PARENT::getIdValue( );
    }

}
?>
