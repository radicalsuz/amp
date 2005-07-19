<?php

require_once( "AMP/System/Data/Item.inc.php" );

class PaymentItem extends AMPSystem_Data_Item {

    var $currency_format = '$%5.2f US';
    var $datatable = "payment_items";

    function PaymentItem( &$dbcon, $id=null ) {
        $this->init($dbcon, $id);
    }

    function optionValue() {
        return sprintf( $this->currency_format, $this->getData('Amount') ) ."&nbsp;&nbsp;&nbsp;". $this->getData('name');

    }

}
?>
