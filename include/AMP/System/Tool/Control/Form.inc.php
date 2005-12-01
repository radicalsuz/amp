<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/System/Tool/Control/ComponentMap.inc.php');

class ToolControl_Form extends AMPSystem_Form_XML {

    var $name_field = 'description';

    function ToolControl_Form( ) {
        $name = 'module_control';
        $this->init( $name );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'description', '_changeLabel', 'set');
    }
    function _changeLabel( $data, $fieldname ) {
        $this->setFieldLabel( 'setting', $data[$fieldname]);

    }
}

?>
