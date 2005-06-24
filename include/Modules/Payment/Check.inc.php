<?php
require_once ('Modules/Payment/Payment.php');

class PaymentType_Check {

    var $payment;
    var $name = "Check";

    var $check_info = array();
    var $check_info_keys = array ("Check_Number", "Date_Processed");

    function PaymentType_Check ( &$payment ) {
        $this->init( $payment );
    }

    function init( &$payment ) {
        $this->payment = &$payment;
        $this->dbcon = & $payment->dbcon;
    }

    function getFields() {
        $dt_format = array("format"=>"dMY");
        return array(
            'Check_Number' =>  array('type' =>  'text',
                                    'public'=>  false,
                                    'label' =>  'Check Number',
                                    'size'  =>  12,
                                    'enabled'=> true ),
            'Date_Processed' =>  array( 'type'   =>  'date',
                                        'public' =>  false,
                                        'label'  =>  'Date Processed',
                                        'enabled'=>  true,
                                        'values'=>  'today') );
    }

    function getData( $field = null ) {
        if (!isset( $field )) return $this->check_info;

        if (isset($this->check_info[$field])) return $this->check_info[$field];
        return false;
    }

    function getInsertData() {
        return array(
            'Check_Number' =>  $this->check_info['Check_Number'],
            'Date_Processed' => $this->check_info['Date_Processed'] );
    }

    function setData ( $data ) {
        $this->check_info = array_combine_key( $this->check_info_keys, $data );
    }

    function execute( ) {
        return $this->payment->save(); 
    }
}
?>
