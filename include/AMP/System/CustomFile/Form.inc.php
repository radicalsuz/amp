<?php

require_once( 'AMP/System/Form/XML.inc.php');

class AMP_System_CustomFile_Form extends AMPSystem_Form_XML {

    function AMP_System_CustomFile_Form ( ) {
        $name = 'customfile_edit_form';
        $this->init( $name, 'POST', 'customfile.php' );
        $header = & AMP_getHeader( );
    }

    //overrides standard behavior for non-numeric filename ids
    function getIdValue() {
        if ( isset($_REQUEST[ $this->id_field ]) && ( $_REQUEST[ $this->id_field ] )) return $_REQUEST[ $this->id_field ];
        return PARENT::getIdValue( );
    }

}

?>
