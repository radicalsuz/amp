<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'Modules/Payment/Set.inc.php' );

class PaymentList extends AMPSystem_List {
    var $name = "PaymentList";
    var $col_headers = array( 'Item' => 'description', 'Amount'=>'Amount', 'Date'=>'Date_Processed', 'Status'=>'Status', 'Type'=>'Payment_Type');
    var $editlink = "payment.php";

    function PaymentList ( &$dbcon ) {
        $source = & new AMPSystem_Payment_Set ( $dbcon );
        $this->init(  $source );
    }

    function getCustomerTransactions( $user_ID ) {
        $this->source->addCriteria( "user_ID = ".$user_ID );
        return $this->source->readData();
    }


}
?>
