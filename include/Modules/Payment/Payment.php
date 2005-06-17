<?php

require_once ('Modules/Payment/Customer.inc.php');
require_once ('Modules/Payment/CreditCard.inc.php');
require_once ('Modules/Payment/Check.inc.php');

Class Payment {
	var $dbcon;
	var $id;
    var $payment_info_keys = array (
		'user_ID','payment_item_ID','Amount','Date_Processed','Status','Payment_Type');

    var $amount;
    var $description; //Description of what is being purchased

	var $fields;
	var $data;

	var $user_ID;
    var $customer;
    var $paymentType; //CreditCard, Check, or Cash

	function Payment(&$dbcon, $type=null) {
        $this->init($dbcon, $type);
	}

    function init(&$dbcon, $type=null) {
		$this->dbcon = &$dbcon;
        if (isset($type)) $this->setPaymentType ( $type );
        $this->customer = new PaymentCustomer( $this->dbcon );
		$this->_register_fields();
    }

    function readData( $payment_id ) {
        if ( !($payment_data = $this->dbcon->GetRow("Select * from payment where id=".$payment_id))) return false;
        $this->setPaymentType ( $payment_data['Payment_Type'] );
        $this->paymentType->setData($payment_data);
        $this->setCustomer ($payment_data);
        $this->id=$payment_id;
    }

    function setPaymentType ( $type ) {
        $type_class = 'PaymentType_' . $type;
        if (class_exists( $type_class )) {

            $this->paymentType = & new $type_class ( $this );

        }
    }

    /* * * * * * * * * * * * *
    * function setCustomer
    * 
    * initializes Customer information
    * var $data = associative array of values
    * 
    * * * * * * * * * */

    function setCustomer ($data) {    
        if (!is_array($data)) return false;
        $this->customer = new PaymentCustomer ($this->dbcon);

        $this->user_ID = $this->customer->setData($data);

    }

    function prepareTransaction( $data, $options=null ) {
        $this->setCustomer($data);
        $this->setItem( $data );
        $this->setOrder( $data );

        $this->paymentType->prepareTransaction($data, $options);

    }

    function setItem ( $data ) {
        if (isset($data['item_ID'])) $this->payment_item_ID = $data['item_ID'];
    }
    function setOrder ( $data ) {
        if (isset($data['order_ID'])) $this->order_ID = $data['order_ID'];
    }


    function execute( $amount, $description = "generic item" ) {
        $this->description = $description;
        $this->amount = $amount;

        if (!isset($this->id)) {
            return $this->paymentType->execute();
        }
    }
	
    function _register_fields(){
        if (isset($this->paymentType)) {
            $this->fields = $this->paymentType->getFields();
        }
    }

    function getData() { 
        $payment_info['id'] = $this->id;
		$payment_info['user_ID']=$this->user_ID;
        $payment_info['payment_item_ID']=$this->payment_item_ID;
        $payment_info['order_ID']=$this->order_ID;
        $payment_info['Amount']=$this->amount;
        $payment_info['description']=$this->description;
        $payment_info['Date_Processed']=date("r");
        $payment_info['Status']='Processed';
        $payment_info['Payment_Type']=$this->paymentType->name;
        $payment_info['requesting_ip']=$_SERVER['REMOTE_ADDR'];

        return array_merge($payment_info,  $this->paymentType->getData());

    }        

    function setMerchant( $id ) {
        if (method_exists($this->paymentType, 'setMerchant')) {
            $this->paymentType->setMerchant($id);
        }
    }


	function save() {
        $save_data=$this->getData();
        $rs = $this->dbcon->Replace("payment", $save_data, "id", $quote = true );

        if ($rs == ADODB_REPLACE_INSERTED ) $this->id = $this->dbcon->Insert_ID();
        if ($rs) return true;

        return false;
	
	}

}
?>
