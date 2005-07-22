<?php

require_once ( 'AMP/System/Form.inc.php' );
require_once('AMP/System/XMLEngine.inc.php');

class PaymentItem_Form extends AMPSystem_Form {

    function PaymentItem_Form () {
        $name = "AMP_PaymentItem";
        $this->init( $name );
        $this->addFields( $this->readFields() ); 
        
    }

    function readFields() {
        $fieldsource = & new AMPSystem_XMLEngine( "Modules/Payment/Item/Fields" );

        if ( $fields = $fieldsource->readData() ) return $fields;

        return false;

    }
}
?>
