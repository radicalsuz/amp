<?php

require_once( 'AMP/Form/XML.inc.php' );
require_once( 'AMP/Form/TemplateSearch.inc.php' );

/**
 * AMPSearchForm 
 * 
 * @uses AMPForm_XML
 * @package Form 
 * @version 3.5.4
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMPSearchForm extends AMPForm_XML {
	var $xml_pathtype = "search_fields";
    var $_component_header = "Search Items";
    var $submit_button = array(
        'submit' => array(
            'type' => 'submit',
            'public' => true,
            'attr' => array( 'class' => 'searchform_element' ),
            'separator' => 'endform',
            'label'=> 'Search',
            'default'=>'AMPSearch')
        );

    /**
     * AMPSearchForm 
     * 
     * @param mixed $name 
     * @param string $method 
     * @param mixed $action 
     * @access public
     * @return void
     */
    function AMPSearchForm( $name, $method="GET", $action=null) {
        $this->init( $name, $method, $action );
    }

    function init( $name, $method="GET", $action=null ) {
        parent::init( $name, $method, $action );
        $this->defineSubmit( 'AMPSearch', 'Search' );
        unset ($this->template);
        $this->template = &new AMPFormTemplate_Search();
    }

    function submitted() {
        $search_request = ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'search' )
                          || ( isset( $_REQUEST['AMPSearch'] ) && $_REQUEST[ 'AMPSearch' ]));
        if ( !$search_request ) return false;
        return 'search';
    }

    function adjustSubmit() {
        foreach ($this->submit_button as $button_name => $bDef ) {
            $this->submit_button[ $button_name ]['attr'] = array( 'class' => 'searchform_element' );
        }
    }

    function _confirmFieldDef( $field_def ) {
        if ( !( isset( $field_def['attr']) && isset( $field_def['attr']['class']))) {
            $field_def['attr']['class'] = 'searchform_element';
        }
        return parent::_confirmFieldDef(  $field_def );
    }

    function getSearchValues() {
        $search_values = array();

        $values = $this->getValues();

        foreach ($values as $ukey => $uvalue) {
            if (!isset( $this->fields[ $ukey ] )) continue;
            if ( $uvalue==="" ) continue;
            if ( ($this->fields[$ukey]['type']=='text') && ($uvalue == $this->fields[$ukey]['label'])) continue;
            if ( ($this->fields[$ukey]['type']=='date') && is_array( $uvalue )) {
                $search_values[ $ukey ] =  mktime(0,0,0, $uvalue['M'], $uvalue['d'], $uvalue['Y']);
                continue;
            }
            
            $search_values[ $ukey ] = $uvalue;
        }

        return $search_values;
    }

    function getComponentHeader() {
        return $this->_component_header;
    }

	function _getTemplate( $type=null, $separator ) {
        if (!isset($type)) return false;
        return $this->template->getTemplate( $type, $separator );
    }


    function &_addElementSelect( $name, $field_def ) {
        $valueset = $this->_getValueSet( $name );
        $label = $field_def['label'];
		if ( is_array( $valueset ) ) {
            $topvalue = "Select One";
            if (substr($label,-1)!=":") {
                $topvalue = $label;
                $label = "";
            }
            $valueset = array('' => $topvalue ) + $valueset;
        }
        return $this->form->addElement( 'select', $name, $label, $valueset);
    }

    function &_addElementDefault ( $name, $field_def ) {
        $defaults = $this->_getDefault( $name );
        $final_label = $field_def['label'];
        if ( $label = $this->_useInternalizedLabel( $field_def ) ) {
            $this->setDefaultValue( $name, $label );
            $final_label = "";
        }
        return $this->form->addElement( $field_def['type'], $name, $final_label, $defaults );
        
    }

    function _useInternalizedLabel( $field_def ) {
        if (substr($field_def['label'], -1)!=":" && $field_def['type']=='text') return $field_def['label'];
        return false;
    }

    function _adjustElementText( &$fRef, $field_def ) {
        $this->_makeVanishingLabel ( $fRef, $field_def );
        if (!( $size = $field_def['size'])) return;
        $fRef->setSize( $size );
    }

    function _makeVanishingLabel( &$fRef, $field_def ) {
        if (!($label = $this->_useInternalizedLabel( $field_def ))) return; 

        $new_attr = array();
        if (!isset($field_def['attr']['onclick'])) $new_attr['onclick'] = 'if (this.value==\''.$label.'\') this.value=\'\';' ; 
        if (!isset($field_def['attr']['onblur'])) $new_attr['onblur'] = 'if (this.value==\'\') this.value=\''.$label.'\';' ; 
        if (!empty( $new_attr )) $fRef->updateAttributes( $new_attr );
    }


}
?>
