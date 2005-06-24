<?php

/*****
 *
 * UserDataPlugin_EmailReceipt_AMPPayment
 *
 * Email a given user with a record of their purchase
 *
 *****/

require_once('AMP/UserData/Plugin/Email.inc.php');
require_once( 'Modules/Payment/Receipt.inc.php' );

class UserDataPlugin_EmailReceipt_AMPPayment extends UserDataPlugin_Email {

    var $short_name  = 'EmailReceipt_AMPPayment';
    var $long_name   = 'Email Receipt';
    var $description = 'Provide a record of payment to the customer';

    var $available = true;


    function UserDataPlugin_EmailReceipt_AMPPayment ( &$udm, $plugin_instance=null ) {
        $this->init($udm, $plugin_instance);
    }

    function _register_options_dynamic() {
        $this->options['subject']['default']='Purchase Confirmed';
        $this->options['_payment_id'] = array( 'available'=>'false',
                                        'type'=> true );
    }

    function prepareMessage ( $options = null ) {

        $options = array_merge($this->getOptions(), $options);

        if (!isset($options['_payment_ID'])) return false;

        //Show other recorded info
        $text_options['skip_prefix'] = "plugin_AMPPayment_";
        $message = $this->udm->output( 'Text', $text_options );

        //Generate Receipt
        if (!($receipt = & new PaymentReceipt( $this->dbcon, $options['_payment_ID'] ))) return false;
        $message .= $receipt->output();

        return $message;

    }

    function preProcess () {
        $answer = $this->udm->getData( array('Email') );
        if ( $answer['Email'] ) {
            $this->options['mailto']['default'] = $answer['Email'];
            return true;
        }

        //if no email is found, don't try to send
        return false;

        
    }
    
}

?>
