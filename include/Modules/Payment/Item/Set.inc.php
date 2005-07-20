<?php

require_once( 'AMP/System/Data/Set.inc.php' );

class PaymentItemSet extends AMPSystem_Data_Set {

    var $currency_format = '$%5.2f US';
    var $datatable = "payment_items";

    function PaymentItemSet ( &$dbcon ) {
        $this->init( $dbcon );
    }

    function optionValues() {
        if (!$this->isReady()) return false;
        $result = array();
        while( $data = $this->getData() ) {
            $result[ $data[ $this->id_field ] ] = sprintf( $this->currency_format, $data['Amount'] ) ."&nbsp;&nbsp;&nbsp;". $data['name'];
        }
        return $result;

    }
}
?>
