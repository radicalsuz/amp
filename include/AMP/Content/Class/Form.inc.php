<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Class/ComponentMap.inc.php');

class Class_Form extends AMPSystem_Form_XML {

    var $name_field = 'class';

    function Class_Form( ) {
        $name = 'class';
        $this->init( $name );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'id', '_setIdDisplay', 'set'); 
    }
    function _setIdDisplay( $data, $fieldname ){
        if ( !( isset( $data['id']) && $data['id'] )) return false;
        require_once( 'AMP/Content/Display/HTML.inc.php');
        $renderer = &new AMPDisplay_HTML;
        $value = $renderer->in_P( 'ID: ' . $data['id'], array( 'class' => 'name'));
       
        $this->addField( 
             array( 
                'type' => 'static',
                'default' =>  $value,
                ), 
            'id_display' );
        return $data[ $fieldname ]  ;

    }
    function _formFooter( ){
        if ( !$this->getIdValue( )) return false;
        $renderer = &new AMPDisplay_HTML;
        return $renderer->inSpan( AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_EDIT, array( 'class' => 'intitle'))  
                . AMP_navCountDisplay_Class( $this->getIdValue( ) );
    }
}
?>
