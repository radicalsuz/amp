<?php

require_once ('Modules/Payment/Customer.inc.php');
require_once ('Modules/Payment/CreditCard.inc.php');
require_once ('Modules/Payment/Check.inc.php');

Class Payment {
	var $dbcon;
	var $id;

    var $payment_info;
    var $payment_info_keys = array (
		'user_ID','payment_item_ID','order_ID','Amount','Date_Processed','Status','Payment_Type');

	var $fields;
    var $errors;

	var $user_ID;
    var $customer;
    var $paymentType; //CreditCard, Check

	function Payment(&$dbcon, $type=null) {
        $this->init($dbcon, $type);
	}

    function init(&$dbcon, $type=null) {
		$this->dbcon = &$dbcon;
        if (isset($type)) $this->setPaymentType ( $type );
        $this->customer = new PaymentCustomer( $this->dbcon );
		$this->_register_fields();
    }

    function execute( $amount, $description = "generic item" ) {
        $this->setPayment ( array('Amount' => $amount, 'description'=>$description ) );

        return $this->paymentType->execute();
    }


	function save() {
        $save_data=$this->_getInsertData();
        $rs = $this->dbcon->Replace("payment", $save_data, "id", $quote = true );

        if ($rs == ADODB_REPLACE_INSERTED ) $this->id = $this->dbcon->Insert_ID();
        if ($rs) return true;

        return false;
	
	}

    function addError( $message, $type = null ) {
        if (isset($type)) {
            $this->errors[$type] = $message;
            return;
        }

        $this->errors[] = $message;
    }
	
    
    ##########################################
    ###  Public Data Assignment Functions  ###
    ##########################################

    function readData( $payment_id ) {
        if ( !($payment_data = $this->dbcon->GetRow("Select * from payment where id=".$payment_id))) return false;

        $this->id=$payment_id;
        $this->setData( $payment_data );

        return true;
    }

    function setData( $data, $options=null ) {
        $this->setPayment ($data);
        $this->setCustomer($data);
        $this->setItem( $data );
        $this->setOrder( $data );

        $this->paymentType->prepareTransaction($data, $options);

    }

    function setPayment ( $data ) {
        $this->payment_info = array_merge( $this->payment_info, array_combine_key ( $payment_info_keys, $data ));
    }


    function setPaymentType ( $type ) {
        $type_class = 'PaymentType_' . $type;
        if (!class_exists( $type_class )) return false;
        $this->paymentType = & new $type_class ( $this );
        $this->setPayment( array( 'Payment_Type' => $type ) );

    }

    function setCustomer ($data) {    
        if (!is_array($data)) return false;
        $this->customer = new PaymentCustomer ($this->dbcon);
        $this->user_ID = $this->customer->setData($data);

    }

    function setItem ( $data ) {
        if (isset($data['item_ID'])) $this->payment_data['payment_item_ID'] = $data['item_ID'];
    }

    
    #########################################
    ###  Public Data Retrieval Functions  ###
    #########################################

    function getData ( $fieldname = null ) {
        if (!isset($fieldname)) return $this->payment_info;

        if (isset($this->payment_info[$fieldname])) return $this->payment_info[$fieldname];
        return false;
    }

    
    ###################################
    ###  Private Utility Functions  ###
    ###################################


    function _register_fields(){
        if (isset($this->paymentType)) {
            $this->fields = $this->paymentType->getFields();
        }
    }

    function _getInsertData() { 
        $payment_info = $this->payment_info;
        $payment_info['id'] = $this->id;
        $payment_info['Date_Processed']=date("r");
        $payment_info['Status']='Processed';
        $payment_info['requesting_ip']=$_SERVER['REMOTE_ADDR'];

        return array_merge($payment_info,  $this->paymentType->getData());

    }        


}
?>
