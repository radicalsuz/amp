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
    }

    function adjustFields( $fields ) {
        $id_display = $this->_setIdDisplay( );
        if ( $id_display ) $fields['id_display'] = $id_display;
        return $fields;
    }

    function _setIdDisplay( ){
        if ( !( $id = $this->getIdValue( ))) return false;
        require_once( 'AMP/Content/Display/HTML.inc.php');
        $renderer = &new AMPDisplay_HTML;
        $value = $renderer->in_P( 'ID: ' . $id, array( 'class' => 'name'));
       
        return
             array( 
                'type' => 'static',
                'default' =>  $value,
                ); 
    }

    function _selectAddNull( $valueset, $name ) {
        if ( $name != 'parent' ) return parent::_selectAddNull( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_SITE_NAME . ' --') + $valueset;
    }

    function _blankValueSet( $valueset, $name ){
        if ( $name != 'parent' ) return parent::_blankValueSet( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_SITE_NAME . ' --');

    }

    function _formFooter( ){
        if ( !$this->getIdValue( )) return false;
        $renderer = &new AMPDisplay_HTML;
        return $renderer->inSpan( AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_EDIT, array( 'class' => 'intitle'))  
                . AMP_navCountDisplay_Section( $this->getIdValue( ) );
    }
}
?>
