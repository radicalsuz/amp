<?php

Class Payment {
	var $dbcon;
	var $user_ID;
	var $payment_ID;
    var $amount;
    var $description; //Description of what is being purchased
	var $fields;
	var $data;
    var $type; //CreditCard, Check, or Cash
    var $payment_info_keys = array (
		'user_ID','payment_item_ID','Amount','Date_Processed','Status','Payment_Type');

	function Payment(&$dbcon) {
        $this->init($dbcon,'');
	}

    function init(&$dbcon, $type) {
		$this->dbcon = &$dbcon;
		$this->_register_fields();
    }
	
    function _register_fields(){
#       placeholder -- should be overriden by Payment Type Extension
    }

    function getData() { 
		$payment_info['user_ID']=$this->user_ID;
        $payment_info['payment_item_ID']=$this->payment_item_ID;
        $payment_info['order_ID']=$this->order_ID;
        $payment_info['Amount']=$this->amount;
        $payment_info['description']=$this->description;
        $payment_info['Date_Processed']=date();
        $payment_info['Status']='Processed';
        $payment_info['Payment_Type']=$this->type;

        $method_data = 'getData_'.$this->type;
        if (method_exists($this, $method_data)) $payment_info = array_merge($payment_info, $this->$method_data());
        return $payment_info;

    }        

	function save() {
        $save_data=$this->getData();
        $sql = $this->payment_ID?   $this->updateSQL ( $save_data ):
                                    $this->insertSQL ( $sqve_data );
        
        $rs = $this->dbcon->CacheExecute( $sql ) or
                    die( "Unable to save payment data using SQL $sql: " . $this->dbcon->ErrorMsg() );

        if ($rs) {
            $this->payment_ID = $this->dbcon->Insert_ID();
            return true;
        }

        return false;
	
	}

    function updateSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $sql = "UPDATE payment SET ";

        foreach ($data as $field => $value) {
            $elements[] = $field . "=" . $dbcon->qstr( $value );
        }

        $sql .= implode( ", ", $elements );
        $sql .= " WHERE id=" . $dbcon->qstr( $this->payment_ID );

        return $sql;

    }

    function insertSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $fields = array_keys( $data );
        $values_noescape = array_values( $data );

        foreach ( $values_noescape as $value ) {
            $values[] = $dbcon->qstr( $value );
        }

        $sql  = "INSERT INTO payment (";
        $sql .= join( ", ", $fields ) .
                ") VALUES (" .
                join( ", ", $values ) .
                ")";

        return $sql;

    }

    
	
	
	


}
?>
