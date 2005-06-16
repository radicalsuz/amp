<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'Modules/Payment/Payment.php' );

class PaymentList extends AMP_SystemList {
    var $payment;
    var $name = "PaymentList";
    var $datatable = "payment";
    var $column_headers = array( 'Item' => 'description', 'Amount'=>'Amount', 'Date'=>'Date_Processed', 'Status'=>'Status', 'Type'=>'Payment_Type');

    function PaymentList ( &$dbcon ) {
        $this->payment = &new Payment( $dbcon );
        $this->fields = $this->payment->payment_info_keys;
        $this->init(  $dbcon );
    }

    function getCustomerTransactions( $user_ID ) {
        $this->criteria = "user_ID = ".$user_ID;
        return $this->getData();
    }


}

