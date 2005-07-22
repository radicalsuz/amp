<?php

require_once ( 'AMP/System/Form.inc.php' );
require_once ( 'AMP/System/XMLEngine.inc.php' );

class AMPSystem_Region_Form extends AMPSystem_Form {
    var $name_field = "title";

    function AMPSystem_Region_Form() {
        $name = "AMP_RegionDef";
        $this->init( $name );
        $this->addFields( $this->readFields() );
    }


    function readFields() {

        $fieldsource = & new AMPSystem_XMLEngine( 'AMP/System/Region/Fields' );

        if ( $fields = $fieldsource->readData() )     return $fields;

        return false;

    }
}
?>
