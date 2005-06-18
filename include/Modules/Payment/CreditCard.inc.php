<?php 
require_once ('Modules/Payment/Payment.php');
require_once ('Modules/Payment/CreditCard/CC_Functions.inc.php');
require_once ('Modules/Payment/CreditCard/Validator.inc.php');
require_once ('Modules/Payment/CreditCard/Merchant.inc.php');

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
        'auth_code',
        'transaction_id',
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

    /* * * * * * * * * * * * *
    * function Payment_CreditCard
    * 
    * constructor
    * calls base class init
    * sets the active Merchant Account
    * sets the CC Processing Library
    * 
    * * * * * * * * * */


    function PaymentType_CreditCard( &$payment ) {
        $this->init( $payment );
        $this->CC_Library = &new CC_Functions();
    }

    function init( &$payment ) {
        $this->payment = &$payment;
        $this->dbcon = & $payment->dbcon;
    }

    #########################
    ###  Core Functions   ###
    #########################

	function execute() {

        if (!$this->_confirmTransactionisReady()) return false;

        $this->_preTransactionSave();
        $ChargeResult = $this->_chargeCard();
        $this->_postTransactionSave( $ChargeResult );

        if ($ChargeResult['return_code'] != PAYMENT_CC_TRANSACTION_SUCCESS) {
            $this->_aggregateErrors( $ChargeResult );
            return false;
        }

        return true;
	}

    /* * * * * * * * * * * * *
    * function getFields()
    * 
    * defines characteristics of Credit Card form fields
    * for use by HTML_QuickForm library
    * 
    * * * * * * * * * */


    function getFields() {


		$fields['Credit_Card_Number'] = array(
            'type'=>'text', 
            'label'=>'Credit Card Number', 
            'required'=>true,  
            'public'=>true,  
            'size'=>16, 
            'enabled'=>true);
		$fields['Credit_Card_Type'] = array(
            'type'=>'select', 
            'label'=>'Credit Card Type', 
            'required'=>true, 
            'public'=>true, 
            'values'=>'Visa,MasterCard',
            'enabled'=>true);

        $this_year = date('Y');
        $date_options = array("format"=>"mY","minYear"=>$this_year,"maxYear"=>($this_year+10));
		$fields['Credit_Card_Expiration'] = array(
            'type'=>'date', 
            'label'=>'Credit Card Expiration', 
            'required'=>true, 
            'public'=>true, 
            'values'=>$date_options, 
            'enabled'=>true);
		$fields['Credit_Card_Security_Code'] = array(
            'type'=>'text', 
            'label'=>'Credit Card Security Code', 
            'required'=>true, 
            'public'=>true, 
            'size'=>3, 
            'enabled'=>true);

        $cardholder_fields = $this->_prefixLabels( $this->payment->customer->fields, 'Cardholder ' );

        return array_merge($fields, $cardholder_fields);
        
    }

    ##################################
    ### Public Data Access Methods ###
    ##################################

    function getInsertData() {

		$data_fields=array (
        'Credit_Card_Type'  =>  $this->card_info['Type'],
        'Credit_Card_Number'=>  $this->_protect( $this->card_info['Number'] ),
        'Credit_Card_Expiration'    =>  $this->card_info['Expiration'],
        'payment_merchant_ID'       =>  $this->merchant->getData('id'));

        $data_fields = array_merge( $data_fields, $this->transaction_info );

        return array_merge($data_fields, $this->payment->customer->getInsertData());
    }

    function setData( $data ) {
        $this->_setMerchant( $data['merchant_ID'] );
        $this->_setCard( $data );
        $this->_setTransaction( $data );
    }



    ###################################
    ### Private Data Access Methods ###
    ###################################


    function _setMerchant($data) {
        if (!isset( $data[ 'merchant_ID' ] )) {
            $this->payment->addError( "No merchant specified" );
            return false;      
        }
        $this->merchant = & new CreditCardMerchant( $this->dbcon, $data['merchant_ID'] );
    }

    function _setCard ($data) {

        if (!is_array($data)) return false;

        $card_data = array();
        array_walk ($data, array(&$this,'_filterCCdata'));
    }

    function _setTransaction( $data ) {
        $this->transaction_info =
            array_merge(    $this->transaction_info, 
                            array_combine_key( $this->transaction_info_keys, $data )
                       );
    }

    ####################################
    ### Private Helper Functions for ###
    ###  Charging the Credit Cards   ###
    ####################################

    function _chargeCard() {
        //Call the Credit Card processing Library
        return  $this->CC_Library->ChargeCreditCard( 
                    $this->payment->customer->getData(),
                    $this->payment->getData('Amount'),
                    $this->card_info['Number'],
                    $this->payment->getData('Description'),
                    $this->card_info['Expiration'],
                    $this->card_info['Security_Code'],
                    $this->merchant->getData('Account_Type'),
                    $this->merchant->getData('Account_Username'),
                    $this->merchant->getData('Merchant'),
                    $this->merchant->getData('Account_Password'),
                    $this->merchant->getData('Partner'),
                    $this->payment->id,
                    $this->options['Email_Merchant'],
                    $this->options['Email_Customer'],
                    $this->merchant->getData('Payment_Transaction'),
                    $this->merchant->getData('Payment_Method'),
                    PAYMENT_CC_DEBUG
            );
    }

    function _aggregateErrors( $ChargeResult ) {
        if ( $ChargeResult['CVV2MATCH']=='N' ) $this->payment->addError( "Card Security Code incorrect" );
        if ( $ChargeResult['AVSZIP']=='N' ) $this->payment->addError( "Supplied Cardholder Address does not match bank record" );
        if ( $ChargeResult['AVSADDR']=='N' ) $this->payment->addError( "Supplied Cardholder Address does not match bank record" );
        if ( $ChargeResult['return_reason'] ) $this->payment->addError ( $ChargeResult['return_reason']);
        else $this->payment->addError ("Credit Card Transaction Failed");
    }

    //Generate a permanent Transaction_ID by saving to the DB
    function _preTransactionSave() {
        //Create transaction metadata
        $this->transaction_info['Amount']=$this->payment->getData('Amount');
        $this->transaction_info['Time_Requested']=time();
        $this->transaction_info['Date_Submitted']=date("Y-m-d");
        $this->transaction_info['Status']='Awaiting Approval';

        $this->payment->save();
    }

    function _postTransactionSave( $ChargeResult ) {
        //complete transaction metadata
        $this->transaction_info['Status']=$this->response_codes[$ChargeResult['return_code']];
        $this->transaction_info['Date_Processed']=date("Y-m-d");
        $this->transaction_info['Time_Responded']=time();
        $this->transaction_info['auth_code'] = $ChargeResult["auth_code"];
        $this->transaction_info['transaction_id'] = $ChargeResult['return_id'];

        //save the record of the transaction
        $this->payment->save();
    }
       

    function _confirmTransactionisReady() {
        if (!isset( $this->merchant )) {
            $this->payment->addError( "No merchant specified" );
            return false;      
        }
        if (!$this->_validateCard() ) {
            $this->payment->addError( "Credit Card Information incomplete" );
            return false;      
        }
        if (!is_numeric($this->payment->getData('Amount')) && $this->payment->getData('Amount')) {
            $this->payment->addError( "Amount must be a positive number" );
            return false;
        }
    }

    #################################
    ### Private Utility Functions ###
    #################################

    function _validateCard() {
        if (!isset($this->card_info['Number'])) return false;
        $validator = new CreditCardValidationSolution;
        $split_exp = split( "/", $this->card_info['Expiration']);

        if ( $validator->validateCreditCard( $this->card_info['Number'], 'en', '', 'Y', $split_exp[0], $split_exp[1] )) {
            return true;
        }

        $this->payment->addError( $validator->CCVSError );
        return false;
    }

    function _filterCCdata ($value, $key) {
        $CCdata_prefix = "Credit_Card_";
        if (substr($key, 0, strlen($CCdata_prefix)) != $CCdata_prefix ) return false;

        $new_key = substr($key, strlen($CCdata_prefix));
        if (array_search($new_key, $this->card_info_keys)===FALSE) return false;

        $this->card_info[ $new_key ] = $value;
    }

    function _prefixLabels ( $fielddefs, $prefix ) {
        if (!is_array($fielddefs)) return false;

        foreach ($fielddefs as $key => $fDef) {
            $fielddefs[$key]['label'] = $prefix . $fDef['label'];
        }
        return $fielddefs;
    }

    function _protect( $cc_number ) {
        return str_repeat( 'XXXX-', 3). substr($cc_number, 12);
    }
        

}

?>
