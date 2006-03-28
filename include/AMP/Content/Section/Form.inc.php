<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Section/ComponentMap.inc.php');

class Section_Form extends AMPSystem_Form_XML {

    var $name_field = 'type';

    function Section_Form( ) {
        $name = 'articletype';
        $this->init( $name );
    }

    function setDynamicValues( ){

        $this->addTranslation( 'date2', '_makeDbDateTime', 'get' );
        $this->setFieldValueSet( 'css', AMPfile_List( 'custom', 'css'));
        $this->addTranslation( 'id', '_setIdDisplay', 'set'); 
        $this->addTranslation( 'css', '_multiselectToText', 'get'); 
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
        $renderer = &new AMPDisplay_HTML;
        return $renderer->inSpan( AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_EDIT, array( 'class' => 'intitle'))  
                . AMP_navCountDisplay_Section( $this->getIdValue( ) );
    }
}
?>
