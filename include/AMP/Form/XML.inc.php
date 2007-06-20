<?php

require_once( 'AMP/Form/Form.inc.php' );
require_once('AMP/System/XMLEngine.inc.php');
require_once('AMP/System/ComponentLookup.inc.php');

class AMPForm_XML extends AMPForm {

    var $fieldFile;
    var $xml_pathtype = "fields";
    var $_submit_value = 'submit';
    
    function AMPForm_XML ( $name, $method=null, $action=null) {
        $this->init( $name, $method, $action );
    }

	function init( $name, $method=null, $action=null ) {
		parent::init( $name, $method, $action );
		if (!($this->addFields( $this->adjustFields( $this->readFields()) ))) {
			 trigger_error ( 'XML Field read failed for ' . get_class( $this ) );
			 return;
		}
		$this->setDynamicValues();
	} 

	function readFields() {
		if (!($file_name = $this->getFieldFile())) return false;

        //check for cached field defs
        $cache_key = AMP_CACHE_TOKEN_XML_DATA . $file_name;
        if ( $fields = &AMP_cache_get( $cache_key )) {
            return $fields;
        }

        //$fieldsource = & new AMPSystem_XMLEngine( $file_name );
        //if ( $fields = $fieldsource->readData() ) return $fields;
        //reload def from XML file
        $fields = $this->_readXML( $file_name );

        if ( $fields ) {
            $fields = array_merge( $fields, $this->_getFieldOverrides( $file_name ));
            AMP_cache_set( $cache_key, $fields );
            return $fields;
        }


        return false;

    }

    function _readXML( $file_name ) {
        $fieldsource = & new AMPSystem_XMLEngine( $file_name );

        if ( $fields = $fieldsource->readData() ) {
            return $fields;
        }
        return false;
    }

    function _getFieldOverrides( $file_name ){
        $override_file_name = str_replace( '.xml', '_Override.xml', $file_name );
        $override_file_path = file_exists_incpath( $override_file_name );
        if ( !$override_file_name ) return array( );

        $field_overrides = $this->_readXML( $override_file_path );
        if ( !$field_overrides ) return array( );
        return $field_overrides;

    }

    function adjustFields( $fields ) {
        //stub
        return $fields;
    }

	function getFieldFile() {
		if (isset($this->fieldFile)) return $this->fieldFile;
		$map = &ComponentLookup::instance( get_class($this));
		if (!$map) return false;
        
		return $map->getPath( $this->xml_pathtype );
	}
	
	function setDynamicValues() {
		//placeholder method, should be overwritten by subclasses
	}

    function defineSubmit( $value, $label = 'Submit'){
        $result = parent::defineSubmit( $value, $label );
        $this->_submit_value = $value;
    }

    function submitted( ){
        return isset($_REQUEST[$this->_submit_value]);
    }

    function getIdValue( ) {
        return false;
        if ( !isset( $this->form )) return false;
        if ( !$this->isBuilt ) return false;
        
        $set = $this->getValues( $this->id_field );
        if ($set) return $set[ $this->id_field ];
        return false;

    }
}
?>
