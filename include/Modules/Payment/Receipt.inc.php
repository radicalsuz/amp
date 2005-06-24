<?php

require_once( "Modules/Payment/Payment.php" );
require_once( "AMP/UserData/Input.inc.php" );

class PaymentReceipt {

    var $payment;
    var $dbcon;
    var $item_format = array ( "description", "payment_item_ID", "Amount", "Status", "Payment_Type" );
    var $item_labels = array ("description" => "Item Title",
                            "payment_item_ID" => "Item #",
                            "Status" => "Transaction Status",
                            "Amount" => "Price");
    var $item_record;
    var $user_record;
    var $payment_method;
    var $business_locale = "USA";


    function PaymentReceipt( &$dbcon, $payment_ID=null ) {
        $this->payment = new Payment ($dbcon);
        $this->dbcon = &$dbcon;
        if (isset($payment_ID)) $this->readPayment( $payment_ID );
    }

    function readPayment( $payment_ID ) {
        $this->payment->readData( $payment_ID );
        $this->payment_method = $this->payment->paymentType->getData();
        $this->item_record = array_combine_key( $this->item_format, $this->payment->getData() );

        $this->readItem( $this->item_record['payment_item_ID'] );
        $this->readUser( $this->payment->getData('user_ID') );

    }

    function readItem( $item_id ) {
        $item = &new PaymentItem ($this->dbcon, $item_id);
        if (!isset($item->id)) return false;
        
        $this->item_record['description'] = $item->getData('description'); 
        $this->item_record['Amount'] = sprintf( $item->currency_format, $item->getData('Amount') );
    }

    function readUser( $user_ID ) {
        if (!($user_ID)) return false;
        if (!($user_record = UserDataInput::returnSingleUser( $user_ID, $this->dbcon ))) return; 
        $this->user_record['Name'] = $user_record['First_Name'] . " " . $user_record['Last_Name'];
        $this->user_record['Email'] = $user_record['Email'];
    }


    function output( $format="Text" ) {
        $format_method = "output".$format;
        if (method_exists( $this, $format_method )) {
            return $this->$format_method();
        }
        return false;
    }

    function outputText() {
        $header = 
        '-----------------------------------------'."\n".
        'Purchase Information for '.$this->user_record['Name']."\n".
        '-----------------------------------------'."\n";

        $output = "\nPurchased From: " . $GLOBALS['SiteName'] ;
        
        foreach ( $this->item_format as $itemkey ) {
            if (!isset($this->item_labels[$itemkey])) continue;
            if (!isset($this->item_record[$itemkey])) continue;
            $output .= "\n".$this->item_labels[$itemkey].": ".$this->item_record[$itemkey];
        }

        $output .= "\n\n -- Payment Method:" . $this->item_record['Payment_Type']. "--";

        foreach ( $this->payment_method as $method_field => $method_data ) {
            if (!$method_data) continue;
            $output .= "\n".str_replace("_", " ", $method_field). ": ". $method_data;
        }

        $output .= 
        "\n\n----------------------------------\n".
        "Total Amount: ".$this->item_record['Amount']."\n".
        "----------------------------------\n";

        return $header . $output;
    }

}
?>
