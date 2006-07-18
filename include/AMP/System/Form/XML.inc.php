<?php

require_once( 'AMP/System/Form.inc.php' );
require_once('AMP/System/XMLEngine.inc.php');
require_once('AMP/System/ComponentLookup.inc.php');

class AMPSystem_Form_XML extends AMPSystem_Form {

	var $fieldFile;
	var $xml_pathtype = "fields";

	function AMPSystem_Form_XML() {
        $this->init( 'Generic_System_Form', 'POST');
	}

	function init( $name, $method=null, $action=null ) {
		PARENT::init( $name, $method, $action );
		if (!($fields =  $this->readFields()) ){
			 trigger_error ( 'XML Field read failed for ' . get_class( $this ) );
			 return;
		}
        $this->addFields( $this->adjustFields( $fields ));
		$this->setDynamicValues();
	} 

	function readFields() {
		if (!($file_name = $this->getFieldFile())) return false;
        if ( $fields = $this->getFieldsCached( $file_name )) {
            return $fields;
        }

        //reload def from XML file
        $fieldsource = & new AMPSystem_XMLEngine( $file_name );

        if ( $fields = $fieldsource->readData() ) {
            $this->cacheFields( $fields, $file_name );
            return $fields;
        }

        return false;

    }

    function getFieldsCached( $field_file_name ){
        $cache = &AMP_get_cache( );
        if ( !$cache ) return false;
        $cache_key = AMP_CACHE_TOKEN_FIELD_DEF . $field_file_name;
        return $cache->retrieve( $cache_key );
    }

    function cacheFields( $field_defs, $field_file_name ){
        $cache = &AMP_get_cache( );
        if ( !$cache ) return false;
        $cache_key = AMP_CACHE_TOKEN_FIELD_DEF . $field_file_name;
        return $cache->add( $cache_key, $field_defs );
    }

    function adjustFields( $fields ){
        //interface
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
}
?>
