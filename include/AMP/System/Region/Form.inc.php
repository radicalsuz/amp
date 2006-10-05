<?php

require_once ( 'AMP/System/Form/XML.inc.php' );

class AMPSystem_Region_Form extends AMPSystem_Form_XML {
    var $name_field = "title";

    function AMPSystem_Region_Form() {
        $name = "AMP_RegionDef";
        $this->init( $name );
//        $this->addFields( $this->readFields() );
    }


    /**
     * readFields 
     * 
     * this form is so old it was written before the System_Form_XML library was completed
     * -- now deprecated --
     *
     * @access public
     * @return void
     *
    function readFields() {

        $fieldsource = & new AMPSystem_XMLEngine( 'AMP/System/Region/Fields' );

        if ( $fields = $fieldsource->readData() )     return $fields;

        return false;

    }
    */
}
?>
