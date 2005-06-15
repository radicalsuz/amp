<?php 
require_once ('Modules/Payment/Payment.php');
require_once ('Modules/Payment/CC_Functions.inc.php');
require_once ('Modules/Payment/CreditCardMerchant.inc.php');

define ('PAYMENT_CC_TRANSACTION_SUCCESS', '1');
/* * * * * * * * * * * * *
* Class Payment_CreditCard
* extends Payment
*
* AMP Build 3.4.7
* 2005-05-10
* Author: Austin Putman, austin@radicaldesigns.org
* 
* * * * * * * * * */


class PaymentType_CreditCard {
    // Information related to the Credit Card to be charged
    var $card_info = array();
    var $card_info_keys = array(
        'Type',
        'Number',
        'Expiration',
        'Security_Code');

    // Information related to the transaction attempt
    var $transaction_info = array();
    var $transaction_info_keys = array(
        'Date_Submitted',
        'Date_Processed',
        'Time_Requested',
        'Time_Responded',
        'Amount',
        'Status');

    //Merchant Account Object
    var $merchant;

    //Parent Payment Object
    var $payment;

    //Translation matrix for transaction status
    var $response_codes= array(
		1 => 'Approved', 
		2 => 'Declined', 
		3 => 'Error' );

    //CC_Processor options
    //if Email_Customer or Email_Merchant is enabled
    //the *Processor* (Verisign, Authorize.net, etc)
    //will send them an ugly email regarding the transaction
    //this is not recommended
    var $options = array (
        'Email_Customer'=>'0',
        'Email_Merchant'=>'0',
        );

    //Object which does the actual processing of Credit Cards
    var $CC_Library;
    var $name = "CreditCard";

    /* * * * * * * * * * * * *
    * function Payment_CrediCard
    * 
    * constructor
    * calls base class init
    * sets the active Merchant Account
    * sets the CC Processing Library
    * 
    * * * * * * * * * */


    function PaymentType_CreditCard( &$payment, $merchant_ID=null) {
        $this->init( $payment );
        if (isset($merchant_ID)) $this->setMerchant($merchant_ID);
        $this->CC_Library = &new CC_Functions();
    }

    function init( &$payment ) {
        $this->payment = &$payment;
        $this->dbcon = & $payment->dbcon;
    }

    /* * * * * * * * * * * * *
    * function setMerchant 
    * 
    * initializes Merchant Account information
    * var merchant_ID = id number of merchant record in payment_merchants table 
    * 
    * * * * * * * * * */

    function setMerchant($merchant_ID) {
        $this->merchant = & new CreditCardMerchant( $this->dbcon, $merchant_ID );
    }

    /* * * * * * * * * * * * *
    * function setCard
    * 
    * initializes Credit Card information
    * var $data = associative array of values
    * 
    * * * * * * * * * */

    function setCard ($data) {

        if (!is_array($data)) return false;

        $card_data = array();
        array_walk ($data, array(&$this,'filterCCdata'));
        /*
        print count($card_data);
        print 'lbagh'.join(", ",array_keys($card_data));

        $card_info['Expiration'] = $this->getExpirationDate( $card_data );
        $card_info = array_combine_key ( $this->card_info_keys, $card_data );

        if (is_array($card_info)) $this->card_info=$card_info;
        */
    }

    function filterCCdata ($value, $key) {
        $CCdata_prefix = "Credit_Card_";
        if (substr($key, 0, strlen($CCdata_prefix)) != $CCdata_prefix ) return false;

        $new_key = substr($key, strlen($CCdata_prefix));
        if (array_search($new_key, $this->card_info_keys)===FALSE) return false;

        $this->card_info[ $new_key ] = $value;
    }

    function prepareTransaction( $data ) {
        $this->setCard( $data );
    }

    function getExpirationDate( $data ) {
        if (isset($data['Expiration'])) return $data['Expiration'];
            
        if (isset($data['Expiration_Month']) &&
            isset($data['Expiration_Year']) ) {

             return $data['Expiration_Month'] . "/" .
                    $data['Expiration_Year'];

        }

        return false;
    }

    /* * * * * * * * * * * * *
    * function getFields()
    * 
    * defines characteristics of Credit Card form fields
    * for use by HTML_QuickForm library
    * 
    * * * * * * * * * */


    function getFields() {


		$fields['Credit_Card_Number'] = array('type'=>'text', 'label'=>'Credit Card Number', 'required'=>true, 'public'=>true, 'size'=>40, 'enabled'=>true);
		$fields['Credit_Card_Type'] = array('type'=>'select', 'label'=>'Credit Card Type', 'required'=>true, 'public'=>true, 'size'=>40, 'values'=>'Visa,Master Card','enabled'=>true);

        $this_year = date('Y');
        $date_options = array("format"=>"mY","minYear"=>$this_year,"maxYear"=>($this_year+10));
		$fields['Credit_Card_Expiration'] = array('type'=>'date', 'label'=>'Credit Card Expiration', 'required'=>true, 'public'=>true, 'values'=>$date_options, 'enabled'=>true);

        $cardholder_fields = $this->prefixLabels( $this->payment->customer->fields, 'Cardholder ' );

        return array_merge($fields, $cardholder_fields);
        
    }

    function prefixLabels ( $fielddefs, $prefix ) {
        if (!is_array($fielddefs)) return false;

        foreach ($fielddefs as $key => $fDef) {
            $fielddefs[$key]['label'] = $prefix . $fDef['label'];
        }
        return $fielddefs;
    }
        

    /* * * * * * * * * * * * *
    * function getData() 
    * 
    * returns data related to the current transaction
    * referenced by the getData function in the Payment base class
    * 
    * * * * * * * * * */

    function getData() {

		$data_fields=array (
        'Credit_Card_Type'  =>  $this->card_info['Type'],
        'Credit_Card_Number'=>  $this->card_info['Number'],
        'Credit_Card_Expiration'    =>  $this->card_info['Expiration'],
        'Date_Submitted'    =>  $this->transaction_info['Date_Submitted'],
        'Date_Processed'    =>  $this->transaction_info['Date_Processed'],
        'Time_Requested'    =>  $this->transaction_info['Time_Requested'],
        'Status'            =>  $this->transaction_info['Status'],
        'Amount'            =>  $this->transaction_info['Amount'],
        'merchant_ID'       =>  $this->merchant->getData('id'));

        return array_merge($data_fields, $this->payment->customer->getDataforSQL());
    }

    /* * * * * * * * * * * * *
    * function execute()
    * 
    * assembles data related to the payment for use by the credit card
    * processor , saves a record of each attempted transaction
    * returns false on error, true on success
    * 
    * * * * * * * * * */

	function execute() {
        $description = $this->payment->amount;
        $amount = $this->payment->amount;

        //Create transaction metadata
        $this->transaction_info['Amount']=$amount;
        $this->transaction_info['Time_Requested']=time();
        $this->transaction_info['Date_Submitted']=date("r");
        $this->transaction_info['Status']='Awaiting Approval';
       
        //Generate a permanent Transation_ID by saving to the DB
        $this->payment->save();

        /*
        print "<b>Before we call the actual charge processing stuff:</b> <pre>";
        print_r($this);
        print "</pre>";
        */
        
        //Call the Credit Card processing Library
        $ChargeResult=
            $this->CC_Library->ChargeCreditCard( 
                    #$this->payment->customer->getData(),
                    $amount,
                    $this->card_info['Number'],
                    $description,
                    $this->card_info['Expiration'],
                    $this->merchant->getData('Account_Type'),
                    $this->merchant->getData('Account_Username'),
                    $this->merchant->getData('Account_Password'),
                    $this->merchant->getData('Partner'),
                    $this->payment->payment_ID,
                    #$this->options['Email_Merchant'],
                    #$this->options['Email_Customer'],
                    $this->merchant->getData('Payment_Transaction'),
                    $this->merchant->getData('Payment_Method'),
                    PAYMENT_CC_DEBUG
            );

        //complete transaction metadata
        $this->transaction_info['Status']=$this->response_codes[$ChargeResult['return_code']];
        $this->transaction_info['Date_Processed']=date("r");
        $this->transaction_info['Time_Responded']=time();

        //save the record of the transaction
        $this->payment->save();

        if ($ChargeResult['return_code'] != PAYMENT_CC_TRANSACTION_SUCCESS) {
            $this->payment->error = $ChargeResult['return_reason'];
            return false;
        }

        return true;
       
        
	    	
	}

    function setData ( $data ) {
        $this->card_info = array_combine_key( $this->card_info_keys, $data );
        $this->transaction_info = array_combine_key( $this->transaction_info_keys, $data );
    }
}

?>
