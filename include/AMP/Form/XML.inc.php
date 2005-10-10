<?php

require_once( 'AMP/Form/Form.inc.php' );
require_once('AMP/System/XMLEngine.inc.php');
require_once('AMP/System/ComponentLookup.inc.php');

class AMPForm_XML extends AMPForm {

    var $fieldFile;
    var $xml_pathtype = "fields";
    
    function AMPForm_XML ( $name, $method=null, $action=null) {
        $this->init( $name, $method, $action );
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
