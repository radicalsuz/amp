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
			 trigger_error ( sprintf( AMP_TEXT_ERROR_XML_READ_FAILED, get_class( $this ) ));
			 return;
		}
        $this->addFields( $this->adjustFields( $fields ));
		$this->setDynamicValues();
	} 

	function readFields() {
		if (!($file_name = $this->getFieldFile())) return false;

        //check for cached field defs
        $cache_key = AMP_CACHE_TOKEN_XML_DATA . $file_name;
        if ( $fields = &AMP_cache_get( $cache_key )) {
            return $fields;
        }

        //reload def from XML file
        $fieldsource = & new AMPSystem_XMLEngine( $file_name );

        if ( $fields = $fieldsource->readData() ) {
            AMP_cache_set( $cache_key, $fields );
            return $fields;
        }

        return false;

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
