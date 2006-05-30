<?php

require_once( 'AMP/System/Form/XML.inc.php');

class AMP_Content_Nav_Location_Form extends AMPSystem_Form_XML {
    var $copier_name = 'nav_locations_copier';
    var $_form_name = 'nav_locations';

    function AMP_Content_Nav_Location_Form( ){
        $this->init( $this->_form_name );
    }

    function setDynamicValues( ){
        #$this->_initCopier( );
    }

    function _initCopier( ){
        require_once( 'AMP/Form/ElementCopierScript.inc.php');
        $this->_getValueSet( 'navid');
        $this->_getValueSet( 'position');

        $copier_fields = array( 
            'navid' => $this->getField( 'navid'),
            'position' => $this->getField( 'position'));

        $this->_copier = &ElementCopierScript::instance( );
        $this->_copier->addCopier( $this->copier_name, $copier_fields, $this->_form_name );

        if ( !empty( $_POST )){
            $this->_copier->addSets( $this->copier_name, $_POST );
        }
        $this->addFields( $this->_copier->getAddButton( $this->copier_name ));

        $header = &AMP_getHeader( );
        $header->addJavascriptDynamic( $this->_copier->output( ), 'copier' );

    }

}


?>
