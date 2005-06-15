<?php


class CreditCardMerchant {

    // Information related to the Merchant Account to be used
	var $merchant_info = array();
	var $merchant_info_keys = array(
        'id',
		'Account_Type', 
		'Account_Username',
		'Account_Password',
        'Partner',
		'Server',
		'Payment_Method',
		'Payment_Transaction',
		'trans_key');

    function CreditCardMerchant ( &$dbcon, $id=null ) {
        $this->init ($dbcon, $id );
    }

    function init (&$dbcon, $id = null) {
        $this->dbcon = &$dbcon;
        if (isset($id)) $this->readData($id);
    }

    function desiredFields() {
        return $this->merchant_info_keys;
    }

    function readData($id) {
		$sql = "Select * from payment_merchants where id = ".$this->dbcon->qstr($id);
		$merchant_record = $this->dbcon->GetRow($sql);
        if (!$merchant_record) return false;

        $this->merchant_info = array_combine_key($this->merchant_info_keys, $merchant_record);

        return $this->merchant_info;
    }

    function getData( $fieldname=null ) {
        if (!isset($fieldname)) return $this->merchant_info;
        if (isset($this->merchant_info[$fieldname])) {
            return $this->merchant_info[ $fieldname ];
        }
        return;
    }
        


}


?>
