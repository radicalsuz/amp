<?php

require_once( 'AMP/System/Form.inc.php' );
require_once('AMP/System/XMLEngine.inc.php');
require_once('AMP/System/ComponentLookup.inc.php');

class AMPSystem_Form_XML extends AMPSystem_Form {

	var $fieldFile;
	var $xml_pathtype = "fields";
	var $name;

	function AMPSystem_Form_XML() {
	}

	function init( $name, $method=null, $action=null ) {
		PARENT::init( $name, $method, $action );
		if (!($this->addFields( $this->readFields()) )){
			 trigger_error ( 'XML Field read failed for ' . get_class( $this ) );
			 return;
		}
		$this->setDynamicValues();
	} 

	function readFields() {
		if (!($file_name = $this->getFieldFile())) return false;
        $fieldsource = & new AMPSystem_XMLEngine( $file_name );

        if ( $fields = $fieldsource->readData() ) return $fields;

        return false;

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
